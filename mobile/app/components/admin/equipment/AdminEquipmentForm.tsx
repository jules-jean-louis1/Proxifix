import React, { useEffect, useState } from 'react';
import { FormProvider, useForm } from 'react-hook-form';
import { View } from 'react-native';
import { AppTextField } from '../../inputs/AppTextField';
import { AppButton } from '../../buttons/AppButton';
import { AppSelectInput } from '../../inputs/AppSelectInput';
import { AppSimpleSearchInput } from '../../inputs/AppSimpleSearchInput';
import { useApi } from '@/app/hooks/useApi';

interface Equipment {
  id: number;
  name: string;
  reference?: string;
  model?: string;
  created_at: string;
  updated_at: string;
  user_id: number;
  brand_id: number;
  type_equipment_id: number;
  operating_system_id?: number;
  user?: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
  };
  brand?: {
    id: number;
    name: string;
  };
  type_equipment?: {
    id: number;
    name: string;
  };
  operating_system?: {
    id: number;
    name: string;
  };
}

interface AdminEquipmentFormProps {
  mode: 'create' | 'edit';
  initialData?: Equipment;
  onSubmit: (data: Equipment) => void;
  onCancel?: () => void;
  onDelete?: (id: number) => void;
}

export const AdminEquipmentForm: React.FC<AdminEquipmentFormProps> = ({
  mode,
  initialData,
  onSubmit,
  onCancel,
  onDelete,
}) => {
  const api = useApi();
  const [brands, setBrands] = useState<{ label: string; value: number }[]>([]);
  const [typeEquipments, setTypeEquipments] = useState<
    { label: string; value: number }[]
  >([]);
  const [operatingSystems, setOperatingSystems] = useState<
    { label: string; value: number }[]
  >([]);

  const methods = useForm<Equipment>({
    defaultValues: initialData || {
      id: 0,
      name: '',
      reference: '',
      model: '',
      created_at: '',
      updated_at: '',
      user_id: 0,
      brand_id: 0,
      type_equipment_id: 0,
      operating_system_id: 0,
    },
  });

  // Load options for dropdowns
  useEffect(() => {
    loadOptions();
  }, [mode, initialData]);

  const loadOptions = async () => {
    try {
      // Load brands
      const brandsResponse = await api.get('/brand');
      const brandsOptions = brandsResponse.data.map((brand: any) => ({
        label: brand.name,
        value: brand.id,
      }));
      setBrands(brandsOptions);

      // Load type equipments
      const typeEquipmentsResponse = await api.get('/type-equipment');
      const typeEquipmentsOptions = typeEquipmentsResponse.data.map(
        (type: any) => ({
          label: type.name,
          value: type.id,
        })
      );
      setTypeEquipments(typeEquipmentsOptions);

      // Load operating systems
      const osResponse = await api.get('/operating-system');
      const osOptions = osResponse.data.map((os: any) => ({
        label: os.name,
        value: os.id,
      }));
      setOperatingSystems(osOptions);
    } catch (error) {
      console.error('Error loading options:', error);
    }
  };

  const handleFormSubmit = (data: Equipment) => {
    onSubmit(data);
  };

  return (
    <View>
      <FormProvider {...methods}>
        <AppTextField
          nameField="name"
          label="Nom de l'équipement"
          rules={{ required: 'Le nom est requis' }}
        />

        <AppTextField nameField="reference" label="Référence" />

        <AppTextField nameField="model" label="Modèle" />

        <AppSimpleSearchInput
          nameField="user_id"
          label="Client propriétaire"
          placeholder="Rechercher un client..."
          searchEndpoint="/user?role=ROLE_CUSTOMER&search="
          displayKey={item =>
            `${item.first_name} ${item.last_name} (${item.email})`
          }
          valueKey="id"
          rules={{ required: 'Le client propriétaire est requis' }}
        />

        <AppSelectInput
          nameField="brand_id"
          label="Marque"
          placeholder="Sélectionnez une marque"
          options={brands}
          rules={{ required: 'La marque est requise' }}
          defaultValue={initialData?.brand_id}
        />

        <AppSelectInput
          nameField="type_equipment_id"
          label="Type d'équipement"
          placeholder="Sélectionnez un type"
          options={typeEquipments}
          rules={{ required: "Le type d'équipement est requis" }}
          defaultValue={initialData?.type_equipment_id}
        />

        <AppSelectInput
          nameField="operating_system_id"
          label="Système d'exploitation"
          placeholder="Sélectionnez un OS (optionnel)"
          options={operatingSystems}
          defaultValue={initialData?.operating_system_id}
        />

        <View
          style={{ flexDirection: 'column', justifyContent: 'space-between' }}
        >
          <AppButton
            children={mode === 'create' ? 'Créer' : 'Mettre à jour'}
            type="primary"
            onPress={methods.handleSubmit(handleFormSubmit)}
          />
          {mode === 'edit' && (
            <AppButton
              children="Supprimer"
              type="tertiary"
              onPress={() => {
                if (initialData) {
                  onDelete?.(initialData.id);
                }
              }}
            />
          )}
          {onCancel && (
            <AppButton children="Annuler" onPress={onCancel} type="secondary" />
          )}
        </View>
      </FormProvider>
    </View>
  );
};
