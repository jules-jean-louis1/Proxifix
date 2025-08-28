import { Feather } from '@expo/vector-icons';
import React, { FC, useState } from 'react';
import { FormProvider, useForm } from 'react-hook-form';
import { View, Text, Modal, StyleSheet, TouchableOpacity } from 'react-native';
import { AppTextField } from '../../inputs/AppTextField';
import { AppSelectInput } from '../../inputs/AppSelectInput';
import { AppButton } from '../../buttons/AppButton';
import { useSessionContext } from '@/app/context/useSessionContext';
import { useApi } from '@/app/hooks/useApi';
import { ScrollView } from 'react-native';

interface EquipmentModalFormProps {
  type: 'create' | 'update' | 'delete';
  equipment?: any;
  brands?: any[];
  typeEquipment?: any[];
  os?: any[];
  setEquipments?: (equipment: any) => void;
  button?: React.ReactElement;
  onSuccess?: () => void;
}
export const EquipmentModalForm: FC<EquipmentModalFormProps> = ({
  type,
  equipment,
  setEquipments,
  brands,
  typeEquipment,
  os,
  button,
  onSuccess = () => {},
}) => {
  const [modalVisible, setModalVisible] = useState<boolean>(false);
  const methods = useForm();
  const { handleSubmit } = methods;
  const sessionCtx = useSessionContext();
  const api = useApi();
  const sessionData = sessionCtx?.session;

  const onSubmit = async (data: any) => {
    const strategies: Record<string, () => Promise<void>> = {
      update: async () => {
        const response = await api.put(`/equipment/${equipment?.id}`, {
          ...data,
          user_id: sessionData?.id,
        });
        setEquipments?.((prev: any) =>
          prev.map((item: any) =>
            item.id === equipment?.id ? response.data : item
          )
        );
        setModalVisible(false);
        onSuccess();
      },
      create: async () => {
        const response = await api.post('/equipment', {
          ...data,
          user_id: sessionData?.id,
        });
        setEquipments?.((prev: any) => [...prev, response.data]);
        setModalVisible(false);
        onSuccess();
      },
    };

    try {
      await strategies[type]?.();
    } catch (error) {
      console.error(error);
    }
  };

  const handleDeleteEquipment = async () => {
    try {
      await api.delete(`/equipment/${equipment?.id}`);
      setEquipments?.((prev: any) =>
        prev.filter((item: any) => item.id !== equipment?.id)
      );
      setModalVisible(false);
    } catch (error) {
      console.error(error);
    }
  };

  const hasIntervention = equipment?.intervention?.length > 0 ? true : false;

  return (
    <View>
      {button &&
        React.cloneElement(button, {
          onPress: () => {
            setModalVisible(true);
          },
        })}
      <Modal
        animationType="slide"
        visible={modalVisible}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={{ flex: 1, justifyContent: 'center', paddingBottom: 16 }}>
          <View style={styles.modalContainer}>
            <View style={styles.modalHeader}>
              <TouchableOpacity
                onPress={() => setModalVisible(false)}
                style={{
                  padding: 10,
                  borderRadius: 50,
                  backgroundColor: '#F0F3F4',
                }}
              >
                <Feather name="x" size={24} color={'#000'} />
              </TouchableOpacity>
            </View>
            <ScrollView style={styles.modalContent}>
              <Text style={styles.title}>
                {type === 'create'
                  ? 'Ajouter un équipement'
                  : 'Modifier un equipement'}
              </Text>
              <FormProvider {...methods}>
                <AppTextField
                  nameField="name"
                  label="Nom"
                  defaultValue={equipment?.name}
                  placeholder="Entrez le nom de l'équipement"
                  rules={{ required: 'Le nom est obligatoire' }}
                />
                <AppSelectInput
                  nameField="type_equipment_id"
                  label="Type"
                  placeholder="Sélectionnez le type d'équipement"
                  defaultValue={equipment?.type_equipment.id}
                  options={typeEquipment!.map(type => ({
                    label: type.name,
                    value: type.id,
                  }))}
                  rules={{ required: 'Le type est obligatoire' }}
                />
                <AppSelectInput
                  nameField="brand_id"
                  label="Marque"
                  placeholder="Sélectionnez la marque"
                  defaultValue={equipment?.brand.id}
                  options={brands!.map(brand => ({
                    label: brand.name,
                    value: brand.id,
                  }))}
                  rules={{ required: 'La marque est obligatoire' }}
                />
                <AppSelectInput
                  nameField="operating_system_id"
                  label="Système d'exploitation"
                  placeholder="Sélectionnez le système d'exploitation"
                  defaultValue={
                    equipment?.operating_system
                      ? equipment.operating_system.id
                      : ''
                  }
                  options={os!.map((os: any) => ({
                    label: os.name,
                    value: os.id,
                  }))}
                />
                <AppButton
                  type="secondary"
                  children="Annuler"
                  onPress={() => {
                    setModalVisible(false);
                    methods.reset();
                  }}
                />
                {type === 'update' ? (
                  <>
                    <AppButton
                      type="primary"
                      children="Modifier"
                      onPress={handleSubmit(data => {
                        onSubmit(data);
                      })}
                    />
                    {hasIntervention ? (
                      <AppButton
                        type="primary"
                        children="Supprimer"
                        onPress={() => handleDeleteEquipment()}
                      />
                    ) : (
                      <Text style={{ textAlign: 'center', marginTop: 10 }}>
                        Un équipement associé à une intervention ne peut pas
                        être supprimé.
                      </Text>
                    )}
                  </>
                ) : (
                  <AppButton
                    type="primary"
                    children="Ajouter"
                    onPress={handleSubmit(data => {
                      onSubmit(data);
                    })}
                  />
                )}
              </FormProvider>
            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  modalContainer: {
    flex: 1,
    backgroundColor: '#fff',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    padding: 16,
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
  title: {
    textAlign: 'center',
    fontSize: 24,
    marginBottom: 20,
    color: '#344260',
  },
});
