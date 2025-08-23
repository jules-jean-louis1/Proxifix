import React from 'react';
import { FormProvider, useForm } from 'react-hook-form';
import { Button, View } from 'react-native';
import { AppTextField } from '../../inputs/AppTextField';
import { AppButton } from '../../buttons/AppButton';
import { AppSelectInput } from '../../inputs/AppSelectInput';

interface Technician {
  id: number;
  email: string;
  role: string;
  first_name: string;
  last_name: string;
  created_at: string;
  updated_at: string;
  company?: any;
  zip_code: string;
  city: string;
  phone: string;
  address: string;
  password?: string;
}

interface AdminTechnicianFormProps {
  mode: 'create' | 'edit';
  initialData?: Technician;
  onSubmit: (data: Technician) => void;
  onCancel?: () => void;
}

const roleOptions = [
  { label: 'Technicien', value: 'ROLE_TECHNICIAN' },
  { label: 'Admin', value: 'ROLE_ADMIN' },
];

export const AdminTechnicianForm: React.FC<AdminTechnicianFormProps> = ({
  mode,
  initialData,
  onSubmit,
  onCancel,
}) => {
  const methods = useForm<Technician>({
    defaultValues: initialData || {
      id: 0,
      email: '',
      role: 'ROLE_TECHNICIAN',
      first_name: '',
      last_name: '',
      created_at: '',
      updated_at: '',
      zip_code: '',
      city: '',
      phone: '',
      address: '',
      password: '',
    },
  });

  return (
    <View>
      <FormProvider {...methods}>
        <AppTextField
          nameField="first_name"
          label="Prénom"
          rules={{ required: 'Le prénom est requis' }}
        />
        <AppTextField
          nameField="last_name"
          label="Nom"
          rules={{ required: 'Le nom est requis' }}
        />
        <AppTextField
          nameField="email"
          label="Email"
          rules={{
            required: "L'email est requis",
            pattern: { value: /^\S+@\S+$/, message: 'Email invalide' },
          }}
        />
        <AppSelectInput
          nameField="role"
          label="Rôle"
          placeholder="Sélectionnez le rôle"
          options={roleOptions}
          rules={{ required: 'Le rôle est requis' }}
        />
        {mode === 'create' && (
          <AppTextField
            nameField="password"
            label="Mot de passe"
            secureTextEntry
            rules={{ required: 'Le mot de passe est requis' }}
          />
        )}
        <AppTextField
          nameField="phone"
          label="Téléphone"
          rules={{
            required: 'Le téléphone est requis',
            pattern: {
              value: /^(?:\d{10}|(?:\d{2}\s){4}\d{2})$/,
              message: 'Numéro de téléphone invalide',
            },
          }}
        />
        <AppTextField
          nameField="zipcode"
          label="Code Postal"
          rules={{
            required: 'Le code postal est requis',
            pattern: { value: /^\d{5}$/, message: 'Code postal invalide' },
          }}
        />
        <AppTextField
          nameField="city"
          label="Ville"
          rules={{ required: 'La ville est requise' }}
        />
        <AppTextField
          nameField="address"
          label="Adresse"
          rules={{ required: "L'adresse est requise" }}
        />
        <View
          style={{ flexDirection: 'column', justifyContent: 'space-between' }}
        >
          <AppButton
            children={mode === 'create' ? 'Créer' : 'Mettre à jour'}
            type="primary"
            onPress={methods.handleSubmit(onSubmit)}
          />
          {onCancel && (
            <AppButton children="Annuler" onPress={onCancel} type="secondary" />
          )}
        </View>
      </FormProvider>
    </View>
  );
};
