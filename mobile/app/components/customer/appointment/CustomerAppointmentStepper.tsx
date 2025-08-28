import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  Alert,
  ActivityIndicator,
  Pressable,
  Modal,
  TouchableOpacity,
} from 'react-native';
import { useForm, FormProvider } from 'react-hook-form';
import { Feather } from '@expo/vector-icons';
import { useApi } from '@/app/hooks/useApi';
import { useSessionContext } from '@/app/context/useSessionContext';
import { AppTextField } from '@/app/components/inputs/AppTextField';
import { AppSelectInput } from '@/app/components/inputs/AppSelectInput';
import { AppButton } from '@/app/components/buttons/AppButton';
import { Text as PaperText } from 'react-native-paper';
import { TimeSlotPicker } from './TimeSlotPicker';
import { CreateEquipmentModal } from '@/app/components/customer/equipment/CreateEquipmentModal';
import { ToolBarCustomer } from '../navigation/ToolBarCustomer';

type StepName = 'equipment' | 'company' | 'date' | 'summary' | 'confirmation';

type Step = {
  name: string;
  title: string;
};

const STEPS: Step[] = [
  { name: 'equipment', title: 'Équipement' },
  { name: 'company', title: 'Entreprise' },
  { name: 'date', title: 'Date & Détails' },
  { name: 'summary', title: 'Récapitulatif' },
  { name: 'confirmation', title: 'Confirmation' },
] as const;

interface Equipment {
  id: number;
  name: string;
  brand?: { name: string };
  operating_system?: { name: string };
  type_equipment?: { name: string };
}

interface Company {
  id: number;
  name: string;
  about: string;
  phone: string;
  address: string;
  city: string;
  specialization: { name: string }[];
}

interface AppointmentData {
  equipment_id: number;
  company_id: number;
  title: string;
  description: string;
  date: string;
  company_spec: string;
}

interface CustomerAppointmentStepperProps {
  mode: 'create' | 'update';
  id?: number;
  button?: React.ReactElement;
  onSuccess?: () => void;
  equipmentId?: number;
}

export const CustomerAppointmentStepper: React.FC<
  CustomerAppointmentStepperProps
> = ({ mode, id, button, onSuccess, equipmentId }) => {
  const [modalVisible, setModalVisible] = useState(false);
  const [activeStep, setActiveStep] = useState<StepName>('equipment');
  const [equipments, setEquipments] = useState<Equipment[]>([]);
  const [companies, setCompanies] = useState<Company[]>([]);
  const [selectedEquipment, setSelectedEquipment] = useState<Equipment | null>(
    null
  );
  const [selectedCompany, setSelectedCompany] = useState<Company | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [appointment, setAppointment] = useState<any>(null);
  const [selectedDateTime, setSelectedDateTime] = useState<Date | null>(null);
  const [selectedTimeSlot, setSelectedTimeSlot] = useState<{
    start: string;
    end: string;
  } | null>(null);
  const [companySpec, setCompanySpec] = useState<any[]>([]);

  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;

  const methods = useForm<AppointmentData>({
    defaultValues: {
      equipment_id: equipmentId || 0,
      company_id: 0,
      title: '',
      description: '',
      date: '',
      company_spec: '',
    },
  });

  const { handleSubmit } = methods;
  const companySpecSelected = methods.watch('company_spec');

  // Charger les équipements du client
  useEffect(() => {
    if (modalVisible && sessionData?.id) {
      loadEquipments();
      loadCompanySpec();
    }
  }, [modalVisible, sessionData?.id]);

  // Charger les données du rendez-vous en mode édition
  useEffect(() => {
    if (modalVisible && mode === 'update' && id) {
      loadAppointmentData();
    }
  }, [modalVisible, mode, id]);

  // Charger les entreprises quand la spécialisation change
  useEffect(() => {
    if (companySpecSelected) {
      loadCompaniesBySpec();
    }
  }, [companySpecSelected]);

  // Pré-sélectionner l'équipement si fourni
  useEffect(() => {
    if (equipmentId && equipments.length > 0) {
      const equipment = equipments.find(eq => eq.id === equipmentId);
      if (equipment) {
        setSelectedEquipment(equipment);
        methods.setValue('equipment_id', equipmentId);
      }
    }
  }, [equipmentId, equipments]);

  // Initialiser les données du rendez-vous en mode édition
  useEffect(() => {
    if (appointment && mode === 'update') {
      methods.setValue('equipment_id', appointment.equipment?.id || 0);
      methods.setValue('company_id', appointment.company?.id || 0);
      methods.setValue('title', appointment.title || '');
      methods.setValue('description', appointment.description || '');
      methods.setValue('date', appointment.date || '');

      // Initialiser les états
      if (appointment.date) {
        const appointmentDate = new Date(appointment.date);
        setSelectedDateTime(appointmentDate);

        const startTime = appointmentDate.toTimeString().slice(0, 8);
        const endDate = new Date(appointmentDate.getTime() + 60 * 60 * 1000);
        const endTime = endDate.toTimeString().slice(0, 8);

        setSelectedTimeSlot({ start: startTime, end: endTime });
      }

      setSelectedEquipment(appointment.equipment || null);
      setSelectedCompany(appointment.company || null);
    }
  }, [appointment, mode, methods]);

  const loadEquipments = async () => {
    try {
      setIsLoading(true);
      const response = await api.get(`/equipment?user_id=${sessionData?.id}`);
      setEquipments(response.data || []);
    } catch (error) {
      console.error('Erreur lors du chargement des équipements:', error);
      Alert.alert('Erreur', 'Impossible de charger vos équipements');
    } finally {
      setIsLoading(false);
    }
  };

  const loadCompanySpec = async () => {
    try {
      const response = await api.get('/company-specialization');
      setCompanySpec(response.data || []);
    } catch (error) {
      console.error('Erreur lors du chargement des spécialisations:', error);
    }
  };

  const loadCompaniesBySpec = async () => {
    try {
      setIsLoading(true);
      const response = await api.get(
        `/company?specialization=${companySpecSelected}`
      );
      setCompanies(response.data || []);
    } catch (error) {
      console.error('Erreur lors du chargement des entreprises:', error);
      Alert.alert('Erreur', 'Impossible de charger les entreprises');
    } finally {
      setIsLoading(false);
    }
  };

  const loadAppointmentData = async () => {
    try {
      setIsLoading(true);
      const response = await api.get(`/appointment?id=${id}`);
      setAppointment(response.data[0]);
    } catch (error) {
      console.error('Erreur lors du chargement du rendez-vous:', error);
      Alert.alert('Erreur', 'Impossible de charger les données du rendez-vous');
    } finally {
      setIsLoading(false);
    }
  };

  const isStepActive = (stepName: StepName) => activeStep === stepName;

  const nextStep = () => {
    // Validation des étapes
    if (activeStep === 'equipment' && !selectedEquipment) {
      Alert.alert('Erreur', 'Veuillez sélectionner un équipement');
      return;
    }

    if (activeStep === 'company' && !selectedCompany) {
      Alert.alert('Erreur', 'Veuillez sélectionner une entreprise');
      return;
    }

    if (activeStep === 'date') {
      const formValues = methods.getValues();
      if (!formValues.title || !formValues.description || !selectedDateTime) {
        Alert.alert('Erreur', 'Veuillez remplir tous les champs requis');
        return;
      }
    }

    const currentStepIndex = STEPS.findIndex(step => step.name === activeStep);
    if (currentStepIndex < STEPS.length - 1) {
      setActiveStep(STEPS[currentStepIndex + 1].name as StepName);
    }
  };

  const previousStep = () => {
    const currentStepIndex = STEPS.findIndex(step => step.name === activeStep);
    if (currentStepIndex > 0) {
      setActiveStep(STEPS[currentStepIndex - 1].name as StepName);
    }
  };

  const handleEquipmentSelect = (equipmentId: number) => {
    const equipment = equipments.find(eq => eq.id === equipmentId);
    if (equipment) {
      setSelectedEquipment(equipment);
      methods.setValue('equipment_id', equipmentId);
    }
  };

  const handleCompanySelect = (companyId: number) => {
    const company = companies.find(comp => comp.id === companyId);
    if (company) {
      setSelectedCompany(company);
      methods.setValue('company_id', companyId);
    }
  };

  const handleFormSubmit = async (data: AppointmentData) => {
    try {
      setIsSubmitting(true);

      const submitData = {
        ...data,
        date: selectedDateTime ? selectedDateTime.toISOString() : data.date,
        user_id: sessionData?.id,
      };

      if (mode === 'create') {
        await api.post('/appointment', { ...submitData, status: 'pending' });
        // Passer à l'étape de confirmation AVANT d'appeler onSuccess
        setActiveStep('confirmation');
        // Ne pas appeler onSuccess ici pour le mode create
      } else if (mode === 'update') {
        await api.put(`/appointment/${id}`, submitData);
        setModalVisible(false);
        Alert.alert('Succès', 'Rendez-vous modifié avec succès');

        if (onSuccess) {
          onSuccess();
        }
      }
    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error);
      Alert.alert(
        'Erreur',
        "Impossible de sauvegarder la demande d'intervention"
      );
    } finally {
      setIsSubmitting(false);
    }
  };

  const resetForm = () => {
    setActiveStep('equipment');
    setSelectedEquipment(null);
    setSelectedCompany(null);
    setSelectedDateTime(null);
    setSelectedTimeSlot(null);
    methods.reset();
  };

  return (
    <View>
      {button &&
        React.cloneElement(button, {
          onPress: () => setModalVisible(true),
        })}

      <Modal
        animationType="slide"
        visible={modalVisible}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={styles.container}>
          {/* Header */}
          <ToolBarCustomer
            title={
              mode === 'create'
                ? "Nouvelle demande d'intervention"
                : 'Modifier le rendez-vous'
            }
            showBack
            icon="x"
            onBackPress={() => setModalVisible(false)}
          />
          <ScrollView style={styles.content}>
            {/* Mode édition simple sans stepper */}
            {mode === 'update' ? (
              <FormProvider {...methods}>
                <View style={styles.stepContent}>
                  <AppSelectInput
                    nameField="company_spec"
                    label="Spécialisation"
                    options={companySpec.map((spec: any) => ({
                      label: spec.label,
                      value: spec.id,
                    }))}
                  />

                  <AppSelectInput
                    nameField="company_id"
                    label="Entreprise"
                    options={companies.map((company: any) => ({
                      label: company.name,
                      value: company.id,
                    }))}
                    rules={{ required: 'Ce champ est requis' }}
                  />

                  <TimeSlotPicker
                    companyId={
                      appointment?.company?.id || methods.watch('company_id')
                    }
                    onSlotSelect={(date, startTime, endTime) => {
                      setSelectedDateTime(date);
                      setSelectedTimeSlot({ start: startTime, end: endTime });
                      methods.setValue('date', date.toISOString());
                    }}
                    selectedDate={selectedDateTime || undefined}
                    selectedTime={selectedTimeSlot?.start}
                  />

                  <AppTextField
                    nameField="title"
                    label="Titre"
                    placeholder="Entrez le titre de l'intervention"
                    rules={{ required: 'Le titre est requis' }}
                  />

                  <AppTextField
                    nameField="description"
                    label="Description"
                    placeholder="Entrez la description de l'intervention"
                    multiline
                    numberOfLines={3}
                  />

                  <View style={styles.buttonContainer}>
                    <AppButton
                      type="secondary"
                      children="Annuler"
                      onPress={() => setModalVisible(false)}
                    />
                    <AppButton
                      type="primary"
                      children="Enregistrer"
                      onPress={handleSubmit(handleFormSubmit)}
                      loading={isSubmitting}
                    />
                  </View>
                </View>
              </FormProvider>
            ) : (
              <>
                {/* Indicateur de progression - Mode création */}
                <View style={styles.progressContainer}>
                  {STEPS.map((step, index) => {
                    const isActive = step.name === activeStep;
                    const isCompleted =
                      STEPS.findIndex(s => s.name === activeStep) > index;

                    return (
                      <View key={step.name} style={styles.stepIndicator}>
                        <View
                          style={[
                            styles.stepCircle,
                            {
                              backgroundColor: isActive
                                ? '#007AFF'
                                : isCompleted
                                  ? '#28A745'
                                  : '#E0E0E0',
                            },
                          ]}
                        >
                          {isCompleted ? (
                            <Feather name="check" size={16} color="white" />
                          ) : (
                            <Text
                              style={[
                                styles.stepNumber,
                                { color: isActive ? 'white' : '#7A7A7A' },
                              ]}
                            >
                              {index + 1}
                            </Text>
                          )}
                        </View>
                        <Text
                          style={[
                            styles.stepTitle,
                            {
                              color: isActive
                                ? '#007AFF'
                                : isCompleted
                                  ? '#28A745'
                                  : '#7A7A7A',
                              fontWeight: isActive ? 'bold' : 'normal',
                            },
                          ]}
                        >
                          {step.title}
                        </Text>

                        {/* Ligne de connexion */}
                        {index < STEPS.length - 1 && (
                          <View
                            style={[
                              styles.connectionLine,
                              {
                                backgroundColor: isCompleted
                                  ? '#28A745'
                                  : '#E0E0E0',
                              },
                            ]}
                          />
                        )}
                      </View>
                    );
                  })}
                </View>

                <FormProvider {...methods}>
                  {/* Étape 1: Sélection de l'équipement */}
                  {isStepActive('equipment') && (
                    <View style={styles.stepContent}>
                      <PaperText
                        variant="titleLarge"
                        style={styles.stepHeading}
                      >
                        Quel équipement voulez-vous faire réparer ?
                      </PaperText>
                      <PaperText
                        variant="bodyMedium"
                        style={styles.stepDescription}
                      >
                        Sélectionnez l'équipement qui nécessite une intervention
                        parmi ceux que vous avez enregistrés.
                      </PaperText>

                      {isLoading ? (
                        <View style={styles.loadingContainer}>
                          <ActivityIndicator size="large" color="#007AFF" />
                          <Text style={styles.loadingText}>
                            Chargement de vos équipements...
                          </Text>
                        </View>
                      ) : (
                        <>
                          <View style={styles.equipmentSection}>
                            <View style={styles.equipmentHeader}>
                              <Text style={styles.sectionTitle}>
                                Vos équipements
                              </Text>
                              <CreateEquipmentModal
                                trigger={
                                  <TouchableOpacity
                                    style={styles.addEquipmentButton}
                                  >
                                    <Feather
                                      name="plus"
                                      size={16}
                                      color="#007AFF"
                                    />
                                    <Text style={styles.addEquipmentText}>
                                      Nouveau
                                    </Text>
                                  </TouchableOpacity>
                                }
                                onSuccess={newEquipment => {
                                  setEquipments(prev => [
                                    ...prev,
                                    newEquipment,
                                  ]);
                                  setSelectedEquipment(newEquipment);
                                  methods.setValue(
                                    'equipment_id',
                                    newEquipment.id
                                  );
                                }}
                              />
                            </View>

                            {equipments.length > 0 ? (
                              <View style={styles.equipmentList}>
                                {equipments.map(equipment => (
                                  <Pressable
                                    key={equipment.id}
                                    style={[
                                      styles.equipmentCard,
                                      selectedEquipment?.id === equipment.id &&
                                        styles.selectedCard,
                                    ]}
                                    onPress={() =>
                                      handleEquipmentSelect(equipment.id)
                                    }
                                  >
                                    <View style={styles.equipmentIcon}>
                                      <Feather
                                        name="monitor"
                                        size={24}
                                        color="#007AFF"
                                      />
                                    </View>
                                    <View style={styles.equipmentInfo}>
                                      <Text style={styles.equipmentName}>
                                        {equipment.name}
                                      </Text>
                                      <Text style={styles.equipmentDetails}>
                                        {equipment.brand?.name
                                          ? `${equipment.brand.name} • `
                                          : ''}
                                        {equipment.type_equipment?.name || ''}
                                      </Text>
                                      {equipment.operating_system?.name && (
                                        <Text style={styles.equipmentOS}>
                                          OS: {equipment.operating_system.name}
                                        </Text>
                                      )}
                                    </View>
                                    {selectedEquipment?.id === equipment.id && (
                                      <Feather
                                        name="check-circle"
                                        size={24}
                                        color="#28A745"
                                      />
                                    )}
                                  </Pressable>
                                ))}
                              </View>
                            ) : (
                              <View style={styles.emptyState}>
                                <Feather
                                  name="monitor"
                                  size={48}
                                  color="#ccc"
                                />
                                <Text style={styles.emptyText}>
                                  Aucun équipement enregistré
                                </Text>
                                <Text style={styles.emptySubtext}>
                                  Créez votre premier équipement pour pouvoir
                                  demander une intervention.
                                </Text>
                              </View>
                            )}
                          </View>

                          <View style={styles.buttonContainer}>
                            <AppButton
                              type="primary"
                              onPress={nextStep}
                              disabled={!selectedEquipment}
                              children="Suivant"
                            />
                          </View>
                        </>
                      )}
                    </View>
                  )}

                  {/* Étape 2: Sélection de l'entreprise */}
                  {isStepActive('company') && (
                    <View style={styles.stepContent}>
                      <PaperText
                        variant="titleLarge"
                        style={styles.stepHeading}
                      >
                        Choisissez une entreprise
                      </PaperText>
                      <PaperText
                        variant="bodyMedium"
                        style={styles.stepDescription}
                      >
                        Sélectionnez d'abord une spécialisation, puis
                        l'entreprise qui effectuera la réparation.
                      </PaperText>

                      <AppSelectInput
                        nameField="company_spec"
                        label="Spécialisation"
                        options={companySpec.map((spec: any) => ({
                          label: spec.label,
                          value: spec.id,
                        }))}
                        rules={{
                          required: 'Veuillez sélectionner une spécialisation',
                        }}
                      />

                      {companySpecSelected && (
                        <>
                          {isLoading ? (
                            <View style={styles.loadingContainer}>
                              <ActivityIndicator size="large" color="#007AFF" />
                              <Text style={styles.loadingText}>
                                Chargement des entreprises...
                              </Text>
                            </View>
                          ) : (
                            <>
                              {companies.length > 0 ? (
                                <View style={styles.companyList}>
                                  {companies.map(company => (
                                    <Pressable
                                      key={company.id}
                                      style={[
                                        styles.companyCard,
                                        selectedCompany?.id === company.id &&
                                          styles.selectedCard,
                                      ]}
                                      onPress={() =>
                                        handleCompanySelect(company.id)
                                      }
                                    >
                                      <View style={styles.companyHeader}>
                                        <View style={styles.companyIcon}>
                                          <Feather
                                            name="briefcase"
                                            size={20}
                                            color="#007AFF"
                                          />
                                        </View>
                                        <View style={styles.companyInfo}>
                                          <Text style={styles.companyName}>
                                            {company.name}
                                          </Text>
                                          <Text style={styles.companyLocation}>
                                            {company.city}
                                          </Text>
                                        </View>
                                        {selectedCompany?.id === company.id && (
                                          <Feather
                                            name="check-circle"
                                            size={24}
                                            color="#28A745"
                                          />
                                        )}
                                      </View>

                                      <Text
                                        style={styles.companyDescription}
                                        numberOfLines={2}
                                      >
                                        {company.about}
                                      </Text>
                                    </Pressable>
                                  ))}
                                </View>
                              ) : (
                                <View style={styles.emptyState}>
                                  <Feather
                                    name="briefcase"
                                    size={48}
                                    color="#ccc"
                                  />
                                  <Text style={styles.emptyText}>
                                    Aucune entreprise disponible
                                  </Text>
                                  <Text style={styles.emptySubtext}>
                                    Aucune entreprise n'est disponible pour
                                    cette spécialisation.
                                  </Text>
                                </View>
                              )}
                            </>
                          )}
                        </>
                      )}

                      <View style={styles.buttonContainer}>
                        <AppButton
                          type="secondary"
                          onPress={previousStep}
                          children="Précédent"
                        />
                        <AppButton
                          type="primary"
                          onPress={nextStep}
                          disabled={!selectedCompany}
                          children="Suivant"
                        />
                      </View>
                    </View>
                  )}

                  {/* Étape 3: Date et détails */}
                  {isStepActive('date') && (
                    <View style={styles.stepContent}>
                      <PaperText
                        variant="titleLarge"
                        style={styles.stepHeading}
                      >
                        Détails de l'intervention
                      </PaperText>
                      <PaperText
                        variant="bodyMedium"
                        style={styles.stepDescription}
                      >
                        Précisez la date souhaitée et décrivez le problème
                        rencontré.
                      </PaperText>

                      <TimeSlotPicker
                        companyId={
                          selectedCompany?.id || methods.watch('company_id')
                        }
                        onSlotSelect={(date, startTime, endTime) => {
                          setSelectedDateTime(date);
                          setSelectedTimeSlot({
                            start: startTime,
                            end: endTime,
                          });
                          methods.setValue('date', date.toISOString());
                        }}
                        selectedDate={selectedDateTime || undefined}
                        selectedTime={selectedTimeSlot?.start}
                      />

                      <AppTextField
                        nameField="title"
                        label="Titre de l'intervention"
                        rules={{ required: 'Le titre est requis' }}
                        placeholder="Ex: Réparation écran cassé"
                      />

                      <AppTextField
                        nameField="description"
                        label="Description du problème"
                        rules={{ required: 'La description est requise' }}
                        placeholder="Décrivez en détail le problème rencontré..."
                        multiline
                        numberOfLines={4}
                      />

                      <View style={styles.buttonContainer}>
                        <AppButton
                          type="secondary"
                          onPress={previousStep}
                          children="Précédent"
                        />
                        <AppButton
                          type="primary"
                          onPress={() => {
                            // Validation avant de passer à l'étape suivante
                            const formValues = methods.getValues();
                            if (!formValues.title || !formValues.description) {
                              Alert.alert(
                                'Erreur',
                                'Veuillez remplir le titre et la description'
                              );
                              return;
                            }
                            if (!selectedDateTime) {
                              Alert.alert(
                                'Erreur',
                                'Veuillez sélectionner une date et heure'
                              );
                              return;
                            }
                            nextStep();
                          }}
                          children="Suivant"
                        />
                      </View>
                    </View>
                  )}

                  {/* Étape 4: Récapitulatif */}
                  {isStepActive('summary') && (
                    <View style={styles.stepContent}>
                      <PaperText
                        variant="titleLarge"
                        style={styles.stepHeading}
                      >
                        Récapitulatif de votre demande
                      </PaperText>
                      <PaperText
                        variant="bodyMedium"
                        style={styles.stepDescription}
                      >
                        Vérifiez les informations avant d'envoyer votre demande.
                      </PaperText>

                      {/* Équipement sélectionné */}
                      <View style={styles.summarySection}>
                        <Text style={styles.summaryTitle}>🔧 Équipement</Text>
                        <View style={styles.summaryCard}>
                          <Text style={styles.summaryLabel}>
                            {selectedEquipment?.name || 'Non sélectionné'}
                          </Text>
                          <Text style={styles.summaryValue}>
                            {selectedEquipment?.brand?.name
                              ? `${selectedEquipment.brand.name} • `
                              : ''}
                            {selectedEquipment?.type_equipment?.name || ''}
                          </Text>
                        </View>
                      </View>

                      {/* Entreprise sélectionnée */}
                      <View style={styles.summarySection}>
                        <Text style={styles.summaryTitle}>🏢 Entreprise</Text>
                        <View style={styles.summaryCard}>
                          <Text style={styles.summaryLabel}>
                            {selectedCompany?.name || 'Non sélectionnée'}
                          </Text>
                          <Text style={styles.summaryValue}>
                            {selectedCompany?.city || ''}
                          </Text>
                        </View>
                      </View>

                      {/* Détails de l'intervention */}
                      <View style={styles.summarySection}>
                        <Text style={styles.summaryTitle}>📋 Détails</Text>
                        <View style={styles.summaryCard}>
                          <Text style={styles.summaryLabel}>Titre</Text>
                          <Text style={styles.summaryValue}>
                            {methods.getValues('title')}
                          </Text>

                          <Text style={styles.summaryLabel}>Description</Text>
                          <Text style={styles.summaryValue}>
                            {methods.getValues('description')}
                          </Text>

                          <Text style={styles.summaryLabel}>
                            Date souhaitée
                          </Text>
                          <Text style={styles.summaryValue}>
                            {selectedDateTime
                              ? selectedDateTime.toLocaleDateString('fr-FR')
                              : 'Non définie'}
                          </Text>
                        </View>
                      </View>

                      <View style={styles.buttonContainer}>
                        <AppButton
                          type="secondary"
                          onPress={previousStep}
                          children="Modifier"
                        />
                        <AppButton
                          type="primary"
                          onPress={handleSubmit(handleFormSubmit)}
                          loading={isSubmitting}
                          children="Envoyer la demande"
                        />
                      </View>
                    </View>
                  )}

                  {/* Étape 5: Confirmation */}
                  {isStepActive('confirmation') && (
                    <View style={styles.confirmationContent}>
                      <View style={styles.successIcon}>
                        <Feather
                          name="check-circle"
                          size={64}
                          color="#28A745"
                        />
                      </View>

                      <PaperText
                        variant="titleLarge"
                        style={styles.confirmationTitle}
                      >
                        Demande envoyée avec succès !
                      </PaperText>

                      <PaperText
                        variant="bodyMedium"
                        style={styles.confirmationText}
                      >
                        Votre demande d'intervention a été transmise à{' '}
                        <Text style={styles.companyNameHighlight}>
                          {selectedCompany?.name || "l'entreprise"}
                        </Text>
                        .
                      </PaperText>

                      <View style={styles.waitingInfo}>
                        <Feather name="clock" size={20} color="#FF9800" />
                        <Text style={styles.waitingText}>
                          Vous devez maintenant attendre que l'entreprise valide
                          la prise en charge de votre équipement.
                        </Text>
                      </View>

                      <PaperText
                        variant="bodySmall"
                        style={styles.nextStepsText}
                      >
                        Vous recevrez une notification dès que l'entreprise aura
                        accepté ou refusé votre demande. Vous pourrez suivre
                        l'avancement dans la section "Mes interventions".
                      </PaperText>

                      <View style={styles.buttonContainer}>
                        <AppButton
                          type="primary"
                          onPress={() => {
                            resetForm();
                            setModalVisible(false);
                            // Appeler onSuccess seulement quand on ferme depuis la confirmation
                            if (onSuccess) {
                              onSuccess();
                            }
                          }}
                          children="Retour à l'accueil"
                        />
                        <AppButton
                          type="secondary"
                          onPress={() => {
                            resetForm();
                            setActiveStep('equipment');
                          }}
                          children="Nouvelle demande"
                        />
                      </View>
                    </View>
                  )}
                </FormProvider>
              </>
            )}
          </ScrollView>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    paddingTop: 50,
  },
  closeButton: {
    padding: 4,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
  },
  content: {
    flex: 1,
    padding: 16,
  },
  progressContainer: {
    flexDirection: 'row',
    marginVertical: 16,
    paddingHorizontal: 8,
  },
  stepIndicator: {
    flex: 1,
    alignItems: 'center',
    position: 'relative',
  },
  stepCircle: {
    width: 30,
    height: 30,
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 4,
  },
  stepNumber: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  stepTitle: {
    fontSize: 10,
    textAlign: 'center',
  },
  connectionLine: {
    position: 'absolute',
    top: 15,
    left: '75%',
    width: '50%',
    height: 2,
    zIndex: -1,
  },
  stepContent: {
    paddingVertical: 16,
  },
  stepHeading: {
    color: '#465270',
    marginBottom: 8,
    textAlign: 'center',
  },
  stepDescription: {
    color: '#7A7A7A',
    marginBottom: 24,
    textAlign: 'center',
    lineHeight: 20,
  },
  loadingContainer: {
    alignItems: 'center',
    paddingVertical: 32,
  },
  loadingText: {
    marginTop: 16,
    color: '#7A7A7A',
  },
  equipmentList: {
    marginBottom: 24,
  },
  equipmentCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#f8f9fa',
    borderRadius: 12,
    marginBottom: 12,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  selectedCard: {
    borderColor: '#007AFF',
    backgroundColor: '#EBF4FF',
  },
  equipmentIcon: {
    marginRight: 12,
  },
  equipmentInfo: {
    flex: 1,
  },
  equipmentName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  equipmentDetails: {
    fontSize: 14,
    color: '#666',
    marginBottom: 2,
  },
  equipmentOS: {
    fontSize: 12,
    color: '#7A7A7A',
  },
  companyList: {
    marginBottom: 24,
  },
  companyCard: {
    padding: 16,
    backgroundColor: '#f8f9fa',
    borderRadius: 12,
    marginBottom: 12,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  companyHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  companyIcon: {
    marginRight: 12,
  },
  companyInfo: {
    flex: 1,
  },
  companyName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    marginBottom: 2,
  },
  companyLocation: {
    fontSize: 14,
    color: '#666',
  },
  companyDescription: {
    fontSize: 14,
    color: '#7A7A7A',
    marginBottom: 12,
    lineHeight: 18,
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 48,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#999',
    marginTop: 16,
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#7A7A7A',
    textAlign: 'center',
    lineHeight: 20,
  },
  buttonContainer: {
    flexDirection: 'column',
    gap: 12,
    marginTop: 24,
  },
  summarySection: {
    marginBottom: 20,
  },
  summaryTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#465270',
    marginBottom: 8,
  },
  summaryCard: {
    backgroundColor: '#f8f9fa',
    padding: 16,
    borderRadius: 8,
  },
  summaryLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  summaryValue: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  confirmationContent: {
    alignItems: 'center',
    paddingVertical: 32,
  },
  successIcon: {
    marginBottom: 24,
  },
  confirmationTitle: {
    color: '#28A745',
    marginBottom: 16,
    textAlign: 'center',
  },
  confirmationText: {
    color: '#333',
    marginBottom: 24,
    textAlign: 'center',
    lineHeight: 20,
  },
  companyNameHighlight: {
    fontWeight: 'bold',
    color: '#007AFF',
  },
  waitingInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFF3CD',
    padding: 16,
    borderRadius: 8,
    marginBottom: 16,
    gap: 12,
  },
  waitingText: {
    flex: 1,
    fontSize: 14,
    color: '#856404',
    lineHeight: 18,
  },
  nextStepsText: {
    color: '#7A7A7A',
    marginBottom: 32,
    textAlign: 'center',
    lineHeight: 18,
  },
  equipmentSection: {
    marginBottom: 24,
  },
  equipmentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
  },
  addEquipmentButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F0F8FF',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 20,
    gap: 4,
  },
  addEquipmentText: {
    fontSize: 14,
    color: '#007AFF',
    fontWeight: '500',
  },
});
