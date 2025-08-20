import { AppButton } from "@/app/components/buttons/AppButton";
import { AppSelectInput } from "@/app/components/inputs/AppSelectInput";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { ToolBarCustomer } from "@/app/components/customer/navigation/ToolBarCustomer";
import { useSessionContext } from "@/app/context/useSessionContext";
import { getStatus } from "@/app/utils/intervention";
import { useApi } from "@/app/hooks/useApi";
import { Feather } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import { FormProvider, useForm } from "react-hook-form";
import { Pressable, ScrollView, View, Alert } from "react-native";
import { Text } from "react-native-paper";
import { AppSimpleSearchInput } from "../../inputs/AppSimpleSearchInput";

type StepName = "detail" | "user" | "status" | "tasks" | "summary" | "confirmation";

type Step = {
  name: string;
  title: string;
};

const STEPS: Step[] = [
  { name: "detail", title: "Détails de l'intervention" },
  { name: "user", title: "Client et technicien" },
  { name: "status", title: "Temps/Status" },
  { name: "tasks", title: "Tâches" },
  { name: "summary", title: "Récapitulatif" },
  { name: "confirmation", title: "Confirmation" },
] as const;

interface Intervention {
  id?: number;
  title: string;
  description: string;
  type_intervention_id: number;
  user_id: number;
  equipment_id: number;
  technician_id?: number;
  status: string;
  company_id: number;
  tasks?: Array<{ title: string; description: string }>;
}

interface AdminInterventionStepperProps {
  mode: "create" | "edit";
  interventionId?: string;
  initialStep?: StepName;
  initialData?: Intervention;
  onSubmit: (data: any) => Promise<void>;
  onCancel?: () => void;
}

export const AdminInterventionStepper: React.FC<
  AdminInterventionStepperProps
> = ({
  mode,
  interventionId,
  initialStep = "detail",
  initialData,
  onSubmit,
  onCancel,
}) => {
  const [steps, setSteps] = useState([...STEPS]);
  const [activeStep, setActiveStep] = useState<StepName>(initialStep);
  const [typeIntervention, setTypeIntervention] = useState<any[]>([]);
  const [user, setUser] = useState<any[]>([]);
  const [companyUser, setCompanyUser] = useState<any[]>([]);
  const [equipment, setEquipment] = useState<any[]>([]);
  const [availableTasks, setAvailableTasks] = useState<any[]>([]); // Tâches disponibles depuis l'API
  const [selectedTasks, setSelectedTasks] = useState<
    Array<{ title: string; description: string; id?: number; name?: string; price?: number }>
  >([]); // Tâches sélectionnées pour cette intervention
  const [interventionStatuses, setInterventionStatuses] = useState<string[]>([
    "pending",
    "assigned",
    "awaiting_pickup",
    "in_progress",
    "completed",
  ]);
  const [isLoading, setIsLoading] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);

  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const isAdmin = sessionCtx?.isAdmin();

  const methods = useForm({
    defaultValues: initialData || {
      title: "",
      description: "",
      type_intervention_id: 0,
      user_id: 0,
      equipment_id: 0,
      technician_id: isAdmin ? 0 : sessionData?.id,
      status: "pending",
      company_id: sessionData?.company?.id || 0,
    },
  });

  const { handleSubmit } = methods;
  const selectedUserId = methods.watch("user_id");

  const getStatusText = (status: string) => {
    switch (status) {
      case "pending":
        return "En attente";
      case "assigned":
        return "Assignée";
      case "awaiting_pickup":
        return "En attente de récupération";
      case "in_progress":
        return "En cours";
      case "completed":
        return "Terminée";
      default:
        return status;
    }
  };

  const isStepActive = (stepName: StepName) => activeStep === stepName;

  const nextStep = () => {
    const currentStepIndex = steps.findIndex(
      (step) => step.name === activeStep
    );
    if (currentStepIndex < steps.length - 1) {
      setActiveStep(steps[currentStepIndex + 1].name as StepName);
    }
  };

  const previousStep = () => {
    const currentStepIndex = steps.findIndex(
      (step) => step.name === activeStep
    );
    if (currentStepIndex > 0) {
      setActiveStep(steps[currentStepIndex - 1].name as StepName);
    }
  };

  // Fonction pour ajouter une tâche sélectionnée
  const addSelectedTask = (taskId: number) => {
    const task = availableTasks.find(t => t.id === taskId);
    if (task && !selectedTasks.find(st => st.id === task.id)) {
      setSelectedTasks([...selectedTasks, task]);
    }
  };

  // Fonctions pour le récapitulatif
  const getSelectedClient = () => {
    const userId = methods.getValues("user_id");
    return user.find(u => u.id === userId);
  };

  const getSelectedTechnician = () => {
    const techId = methods.getValues("technician_id");
    return companyUser.find(u => u.id === techId);
  };

  const getSelectedEquipment = () => {
    const equipId = methods.getValues("equipment_id");
    return equipment.find(e => e.id === equipId);
  };

  const getSelectedTypeIntervention = () => {
    const typeId = methods.getValues("type_intervention_id");
    return typeIntervention.find(t => t.id === typeId);
  };

  const getTotalPrice = () => {
    return selectedTasks.reduce((total, task) => total + (task.price || 0), 0);
  };

  // Charger l'intervention existante si mode edit
  useEffect(() => {
    if (mode === "edit" && interventionId) {
      const loadIntervention = async () => {
        try {
          const response = await api.get(`/intervention/${interventionId}`);
          const intervention = response.data;

          // Pré-remplir le formulaire avec les données existantes
          methods.reset({
            title: intervention.title,
            description: intervention.description,
            type_intervention_id: intervention.type_intervention_id,
            user_id: intervention.user_id,
            equipment_id: intervention.equipment_id,
            technician_id: intervention.technician_id,
            status: intervention.status,
          });

          // Charger les tâches existantes si disponibles
          if (intervention.tasks) {
            setSelectedTasks(intervention.tasks);
          }
        } catch (error) {
          console.error("Erreur lors du chargement de l'intervention:", error);
          Alert.alert("Erreur", "Impossible de charger l'intervention");
          router.back();
        }
      };

      loadIntervention();
    }
  }, [mode, interventionId]);

  // Charger les données de base (types, utilisateurs, etc.)
  useEffect(() => {
    (async () => {
      try {
        const [typeResponse, usersResponse, companyUsersResponse, tasksResponse] =
          await Promise.all([
            api.get(
              `/type-intervention?company_id=${sessionData?.company?.id}`
            ),
            api.get(`/customer?customer_company_id=${sessionData?.company.id}`),
            api.get(
              `/user?company_id=${sessionData?.company?.id}&role=ROLE_TECHNICIAN`
            ),
            api.get(`/task?company_id=${sessionData?.company?.id}`),
          ]);

        setTypeIntervention(typeResponse.data);
        setUser(usersResponse.data);
        setCompanyUser(companyUsersResponse.data);
        setAvailableTasks(tasksResponse.data);
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    })();
  }, []);

  // Charger les équipements quand l'utilisateur change
  useEffect(() => {
    if (selectedUserId) {
      (async () => {
        try {
          const response = await api.get(
            `/equipment?user_id=${selectedUserId}`
          );
          setEquipment(response.data);
        } catch (error) {
          console.error("Error fetching equipment:", error);
        }
      })();
    } else {
      setEquipment([]);
    }
  }, [selectedUserId]);

  const handleFormSubmit = async (data: any) => {
    setIsLoading(true);
    try {
      // Formater les tâches pour le backend (attendues sous 'task' avec juste les ids)
      const formattedTasks = selectedTasks.map(task => ({ id: task.id }));
      
      await onSubmit({
        ...data,
        company_id: sessionData?.company?.id,
        task: formattedTasks, // Backend attend 'task' (pas 'tasks')
      });
      setIsSubmitted(true);
      setActiveStep("confirmation");
    } catch (error) {
      console.error("Error submitting form:", error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title={
          mode === "create" ? "Nouvelle intervention" : "Modifier intervention"
        }
        showBack
        onBackPress={onCancel || (() => router.back())}
        bottomBar
      />

      <ScrollView style={{ flex: 1, padding: 16, backgroundColor: "#fff" }}>
        <View>
          <Text
            variant="bodyMedium"
            style={{ textAlign: "center", color: "#465270", marginBottom: 8 }}
          >
            {mode === "create"
              ? "Veuillez renseigner les détails principaux de l'intervention"
              : "Modifiez les détails de l'intervention"}
          </Text>

          {/* Indicateur de progression */}
          <View
            style={{
              flexDirection: "row",
              marginVertical: 16,
              paddingHorizontal: 8,
            }}
          >
            {STEPS.map((step, index) => {
              const isActive = step.name === activeStep;
              const isCompleted =
                STEPS.findIndex((s) => s.name === activeStep) > index;

              return (
                <View key={step.name} style={{ flex: 1, alignItems: "center" }}>
                  <View
                    style={{
                      width: 30,
                      height: 30,
                      borderRadius: 15,
                      backgroundColor: isActive
                        ? "#007AFF"
                        : isCompleted
                        ? "#28A745"
                        : "#E0E0E0",
                      justifyContent: "center",
                      alignItems: "center",
                      marginBottom: 4,
                    }}
                  >
                    {isCompleted ? (
                      <Feather name="check" size={16} color="white" />
                    ) : (
                      <Text
                        style={{
                          color: isActive ? "white" : "#7A7A7A",
                          fontSize: 12,
                          fontWeight: "bold",
                        }}
                      >
                        {index + 1}
                      </Text>
                    )}
                  </View>
                  <Text
                    style={{
                      fontSize: 10,
                      color: isActive
                        ? "#007AFF"
                        : isCompleted
                        ? "#28A745"
                        : "#7A7A7A",
                      textAlign: "center",
                      fontWeight: isActive ? "bold" : "normal",
                    }}
                  >
                    {step.title}
                  </Text>

                  {/* Ligne de connexion */}
                  {index < STEPS.length - 1 && (
                    <View
                      style={{
                        position: "absolute",
                        top: 15,
                        left: "75%",
                        width: "50%",
                        height: 2,
                        backgroundColor: isCompleted ? "#28A745" : "#E0E0E0",
                        zIndex: -1,
                      }}
                    />
                  )}
                </View>
              );
            })}
          </View>
        </View>

        <FormProvider {...methods}>
          {/* Step 1: Détails de l'intervention */}
          {isStepActive("detail") && (
            <View>
              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8 }}
              >
                Détails de l'intervention
              </Text>
              <Text
                variant="bodyMedium"
                style={{ color: "#465270", marginBottom: 16 }}
              >
                Nom de l'intervention et détails
              </Text>

              <AppTextField
                nameField="title"
                label="Titre de l'intervention"
                rules={{ required: "Le titre de l'intervention est requis" }}
                placeholder="Entrez le titre de l'intervention"
              />

              <AppSelectInput
                nameField="type_intervention_id"
                label="Type d'intervention"
                options={typeIntervention.map((item) => ({
                  label: item.name,
                  value: item.id,
                }))}
                rules={{ required: "Le type d'intervention est requis" }}
                placeholder="Sélectionnez le type d'intervention"
              />

              <AppTextField
                nameField="description"
                label="Description de l'intervention"
                rules={{ required: "La description est requise" }}
                placeholder="Entrez la description de l'intervention"
                multiline
                numberOfLines={4}
              />

              <AppButton type="primary" onPress={nextStep} children="Suivant"/>
            </View>
          )}

          {/* Step 2: Client et technicien */}
          {isStepActive("user") && (
            <View>
              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8 }}
              >
                Client et technicien
              </Text>
              <Text
                variant="bodyMedium"
                style={{ color: "#465270", marginBottom: 16 }}
              >
                Sélectionnez le client et le technicien
              </Text>

              <AppSimpleSearchInput
                nameField="user_id"
                label="Client"
                placeholder="Rechercher un client..."
                searchEndpoint="/customer?query="
                displayKey={(user: any) => `${user.first_name} ${user.last_name}`}
                valueKey="id"
                rules={{ required: "Le client est requis" }}
              />

              <AppSelectInput
                nameField="equipment_id"
                label="Équipement"
                options={
                  selectedUserId && equipment.length > 0
                    ? equipment.map((item) => ({
                        label: item.name,
                        value: item.id,
                      }))
                    : []
                }
                rules={{ required: "L'équipement est requis" }}
                placeholder={
                  selectedUserId 
                    ? equipment.length > 0
                      ? "Sélectionnez l'équipement"
                      : "Aucun équipement disponible"
                    : "Sélectionnez d'abord un client"
                }
              />

              {isAdmin && (
                <AppSelectInput
                  nameField="technician_id"
                  label="Technicien"
                  options={companyUser.map((item) => ({
                    label: `${item.first_name} ${item.last_name}`,
                    value: item.id,
                  }))}
                  rules={{ required: "Le technicien est requis" }}
                  placeholder="Sélectionnez le technicien"
                />
              )}

              <View style={{ flexDirection: "column", gap: 12 }}>  
                <AppButton type="secondary" onPress={previousStep} children="Précédent"/>
                <AppButton type="primary" onPress={nextStep} children="Suivant"/>
              </View>
            </View>
          )}

          {/* Step 3: Statut */}
          {isStepActive("status") && (
            <View>
              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8 }}
              >
                Statut de l'intervention
              </Text>
              <Text
                variant="bodyMedium"
                style={{ color: "#465270", marginBottom: 16 }}
              >
                Sélectionnez le statut de l'intervention
              </Text>

              <AppSelectInput
                nameField="status"
                label="Statut"
                options={interventionStatuses.map((item) => ({
                  label: getStatusText(item),
                  value: item,
                }))}
                rules={{ required: "Le statut est requis" }}
                placeholder="Sélectionnez le statut"
              />

              <View style={{ flexDirection: "column", gap: 12 }}>
                <AppButton type="secondary" onPress={previousStep} children="Précédent"/>
                <AppButton type="primary" onPress={nextStep} children="Suivant"/>
              </View>
            </View>
          )}

          {/* Step 4: Tâches */}
          {isStepActive("tasks") && (
            <View>
              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8 }}
              >
                Tâches de l'intervention
              </Text>
              <Text
                variant="bodyMedium"
                style={{ color: "#465270", marginBottom: 16 }}
              >
                {mode === "create" ? "Ajoutez" : "Modifiez"} les tâches à
                effectuer pour cette intervention (optionnel)
              </Text>

              {/* Sélection des tâches depuis l'API */}
              <View style={{ marginBottom: 16 }}>
                <Text style={{ fontSize: 14, marginBottom: 8, color: "#364A63", fontWeight: "bold" }}>
                  Ajouter une tâche
                </Text>
                
                {availableTasks
                  .filter(task => !selectedTasks.find(st => st.id === task.id))
                  .map((task) => (
                    <Pressable
                      key={task.id}
                      onPress={() => addSelectedTask(task.id)}
                      style={{
                        padding: 12,
                        backgroundColor: "#f8f9fa",
                        borderRadius: 8,
                        marginVertical: 2,
                        borderWidth: 1,
                        borderColor: "#E0E0E0",
                      }}
                    >
                      <View style={{ flexDirection: "row", alignItems: "center" }}>
                        <Feather name="plus-circle" size={16} color="#007AFF" />
                        <View style={{ marginLeft: 8, flex: 1 }}>
                          <Text style={{ fontWeight: "bold", color: "#465270" }}>
                            {task.name || task.title}
                          </Text>
                          <Text style={{ color: "#7A7A7A", fontSize: 12 }}>
                            {task.description} {task.price ? `• ${task.price}€` : ''}
                          </Text>
                        </View>
                      </View>
                    </Pressable>
                  ))}
                
                {availableTasks.filter(task => !selectedTasks.find(st => st.id === task.id)).length === 0 && (
                  <Text style={{ color: "#7A7A7A", fontStyle: "italic", textAlign: "center", padding: 12 }}>
                    Toutes les tâches disponibles ont été ajoutées
                  </Text>
                )}
              </View>

              {/* Affichage des tâches sélectionnées */}
              <Text style={{ marginTop: 16, marginBottom: 8, fontWeight: "bold", color: "#465270" }}>
                Tâches sélectionnées ({selectedTasks.length})
              </Text>

              {selectedTasks.map((task, index) => (
                <View
                  key={index}
                  style={{
                    padding: 12,
                    backgroundColor: "#f5f5f5",
                    borderRadius: 8,
                    marginVertical: 4,
                    flexDirection: "row",
                    justifyContent: "space-between",
                    alignItems: "center",
                  }}
                >
                  <View style={{ flex: 1 }}>
                    <Text style={{ fontWeight: "bold", color: "#465270" }}>
                      {task.name || task.title}
                    </Text>
                    <Text style={{ color: "#7A7A7A" }}>
                      {task.description} {task.price ? `• ${task.price}€` : ''}
                    </Text>
                  </View>
                  <Pressable
                    onPress={() =>
                      setSelectedTasks(selectedTasks.filter((_, i) => i !== index))
                    }
                    style={{ padding: 8 }}
                  >
                    <Feather name="trash-2" size={16} color="#ff4757" />
                  </Pressable>
                </View>
              ))}

              {selectedTasks.length === 0 && (
                <View style={{
                  padding: 16,
                  backgroundColor: "#f8f9fa",
                  borderRadius: 8,
                  alignItems: "center",
                  marginVertical: 8,
                }}>
                  <Text style={{ color: "#7A7A7A", textAlign: "center" }}>
                    Aucune tâche sélectionnée.
                    {"\n"}Utilisez le menu déroulant ci-dessus pour ajouter des tâches.
                  </Text>
                </View>
              )}

              <View style={{ flexDirection: "column", gap: 12, marginTop: 16, marginBottom: 32 }}>
                <AppButton type="secondary" onPress={previousStep} children="Précédent"/>
                <AppButton
                  type="primary"
                  onPress={nextStep}
                  children="Suivant"
                />
              </View>
            </View>
          )}

          {/* Step 5: Récapitulatif */}
          {isStepActive("summary") && (
            <View>
              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8 }}
              >
                Récapitulatif de l'intervention
              </Text>
              <Text
                variant="bodyMedium"
                style={{ color: "#465270", marginBottom: 16 }}
              >
                Vérifiez les informations avant la {mode === "create" ? "création" : "modification"}
              </Text>

              {/* Détails de l'intervention */}
              <View style={{ backgroundColor: "#f8f9fa", padding: 16, borderRadius: 8, marginBottom: 16 }}>
                <Text style={{ fontWeight: "bold", color: "#465270", marginBottom: 8 }}>
                  📋 Détails de l'intervention
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Titre:</Text> {methods.getValues("title")}
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Type:</Text> {getSelectedTypeIntervention()?.name}
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Description:</Text> {methods.getValues("description")}
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Statut:</Text> {getStatusText(methods.getValues("status"))}
                </Text>
              </View>

              {/* Client et Équipement */}
              <View style={{ backgroundColor: "#f8f9fa", padding: 16, borderRadius: 8, marginBottom: 16 }}>
                <Text style={{ fontWeight: "bold", color: "#465270", marginBottom: 8 }}>
                  👤 Client et Équipement
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Client:</Text> {getSelectedClient() ? `${getSelectedClient()?.first_name} ${getSelectedClient()?.last_name}` : 'Non sélectionné'}
                </Text>
                <Text style={{ marginBottom: 4 }}>
                  <Text style={{ fontWeight: "bold" }}>Équipement:</Text> {getSelectedEquipment()?.name || 'Non sélectionné'}
                </Text>
                {isAdmin && (
                  <Text style={{ marginBottom: 4 }}>
                    <Text style={{ fontWeight: "bold" }}>Technicien:</Text> {getSelectedTechnician() ? `${getSelectedTechnician()?.first_name} ${getSelectedTechnician()?.last_name}` : 'Non assigné'}
                  </Text>
                )}
              </View>

              {/* Tâches sélectionnées */}
              <View style={{ backgroundColor: "#f8f9fa", padding: 16, borderRadius: 8, marginBottom: 16 }}>
                <Text style={{ fontWeight: "bold", color: "#465270", marginBottom: 8 }}>
                  🔧 Tâches sélectionnées ({selectedTasks.length})
                </Text>
                {selectedTasks.length > 0 ? (
                  <>
                    {selectedTasks.map((task, index) => (
                      <View key={index} style={{ flexDirection: "row", justifyContent: "space-between", marginBottom: 4 }}>
                        <Text style={{ flex: 1 }}>• {task.name || task.title}</Text>
                        {task.price && <Text style={{ fontWeight: "bold", color: "#007AFF" }}>{task.price}€</Text>}
                      </View>
                    ))}
                    {getTotalPrice() > 0 && (
                      <View style={{ borderTopWidth: 1, borderTopColor: "#E0E0E0", paddingTop: 8, marginTop: 8 }}>
                        <View style={{ flexDirection: "row", justifyContent: "space-between" }}>
                          <Text style={{ fontWeight: "bold", fontSize: 16 }}>Total estimé:</Text>
                          <Text style={{ fontWeight: "bold", fontSize: 16, color: "#007AFF" }}>{getTotalPrice()}€</Text>
                        </View>
                      </View>
                    )}
                  </>
                ) : (
                  <Text style={{ color: "#7A7A7A", fontStyle: "italic" }}>Aucune tâche sélectionnée</Text>
                )}
              </View>

              <View style={{ flexDirection: "column", gap: 12, marginTop: 16 }}>
                <AppButton type="secondary" onPress={previousStep} children="Retour aux tâches"/>
                <AppButton
                  type="primary"
                  onPress={handleSubmit(handleFormSubmit)}
                  loading={isLoading}
                  children={
                    mode === "create"
                      ? "Confirmer et créer l'intervention"
                      : "Confirmer et mettre à jour l'intervention"
                  }
                />
              </View>
            </View>
          )}

          {/* Step 6: Confirmation */}
          {isStepActive("confirmation") && (
            <View style={{ alignItems: "center", paddingVertical: 32 }}>
              <View style={{
                width: 80,
                height: 80,
                borderRadius: 40,
                backgroundColor: "#28A745",
                justifyContent: "center",
                alignItems: "center",
                marginBottom: 24
              }}>
                <Feather name="check" size={40} color="white" />
              </View>

              <Text
                variant="titleLarge"
                style={{ color: "#465270", marginBottom: 8, textAlign: "center" }}
              >
                {mode === "create" ? "Intervention créée avec succès !" : "Intervention mise à jour avec succès !"}
              </Text>

              <Text
                variant="bodyMedium"
                style={{ color: "#7A7A7A", marginBottom: 32, textAlign: "center", lineHeight: 20 }}
              >
                {mode === "create" 
                  ? "Votre intervention a été créée et est maintenant disponible dans la liste des interventions."
                  : "Les modifications ont été enregistrées avec succès."
                }
                {"\n"}
                {selectedTasks.length > 0 && getTotalPrice() > 0 && (
                  `Coût total estimé: ${getTotalPrice()}€`
                )}
              </Text>

              <View style={{ flexDirection: "column", gap: 12, width: "100%" }}>
                <AppButton 
                  type="primary" 
                  onPress={() => router.back()} 
                  children="Retour à la liste"
                />
                {mode === "create" && (
                  <AppButton 
                    type="secondary" 
                    onPress={() => {
                      setActiveStep("detail");
                      setSelectedTasks([]);
                      setIsSubmitted(false);
                      methods.reset();
                    }} 
                    children="Créer une nouvelle intervention"
                  />
                )}
              </View>
            </View>
          )}
        </FormProvider>
      </ScrollView>
    </View>
  );
};
