import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { AppTextField } from '@/app/components/inputs/AppTextField';
import { useSessionContext } from '@/app/context/useSessionContext';
import { FormProvider, useForm } from 'react-hook-form';
import { useApi } from '@/app/hooks/useApi';
import { AppButton } from '@/app/components/buttons/AppButton';
import { router } from 'expo-router';
import { ToolBarCustomer } from '@/app/components/customer/navigation/ToolBarCustomer';

const Profile = () => {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const methods = useForm();
  const { handleSubmit } = methods;
  const api = useApi();
  const [user, setUser] = useState<any>(null);
  const [isLoading, setIsLoading] = useState<boolean>(false);

  useEffect(() => {
    (async () => {
      try {
        setIsLoading(true);
        const resp = await api.get(`/profile`);
        setUser(resp.data);
        setIsLoading(false);
      } catch (e) {
        console.log(e);
      }
    })();
  }, []);

  const onSubmit = async (data: any) => {
    try {
      const response = await api.put(`/profile`, data);
      if (response.status !== 200) {
        return;
      }
    } catch (error) {
      console.error('Error updating profile:', error);
    }
  };

  if (isLoading || !user) {
    return (
      <View style={styles.container}>
        <Text>Chargement...</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Mes coordonnées"
        showBack
        onBackPress={() => {
          router.push('/customer/settings');
          methods.reset();
        }}
        bottomBar
      />
      <ScrollView style={{ flex: 1 }}>
        <View style={styles.form}>
          <Text style={styles.subtitle}>
            Informations de votre compte utilisateur
          </Text>
          <Text style={styles.text}>Mettre à jour vos informations</Text>
          <FormProvider {...methods}>
            <AppTextField
              nameField="email"
              label="Adresse email"
              placeholder="Entrez votre adresse email"
              rules={{
                required: "L'adresse email est obligatoire",
                pattern: {
                  value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                  message: 'Adresse email invalide',
                },
              }}
              defaultValue={user?.email}
            />
            <AppTextField
              nameField="last_name"
              label="Nom"
              placeholder="Entrez votre nom"
              defaultValue={user?.last_name}
              rules={{ required: 'Le nom est obligatoire' }}
            />
            <AppTextField
              nameField="first_name"
              label="Prénom"
              placeholder="Entrez votre prénom"
              defaultValue={user?.first_name}
              rules={{ required: 'Le prénom est obligatoire' }}
            />
            <AppTextField
              nameField="phone"
              label="Téléphone"
              placeholder="Entrez votre numéro de téléphone"
              rules={{
                pattern: {
                  value: /^\d{10}$/,
                  message: 'Numéro de téléphone invalide',
                },
              }}
              defaultValue={user?.phone || ''}
            />
            <AppTextField
              nameField="password"
              label="Mot de passe"
              placeholder="Entrez votre mot de passe"
              secureTextEntry
              defaultValue={''}
            />
            <AppTextField
              nameField="address"
              label="Adresse Postal"
              placeholder="Entrez votre adresse"
              defaultValue={user.address ? user.address : ''}
            />
            <AppTextField
              nameField="zip_code"
              label="Code Postal"
              placeholder="Entrez votre code postal"
              defaultValue={user.zip_code ? user.zip_code : ''}
            />
            <AppTextField
              nameField="city"
              label="Ville"
              placeholder="Entrez votre ville"
              defaultValue={user.city ? user.city : ''}
            />
            <AppButton
              type="primary"
              icon={'chevron-right'}
              children="Mettre à jour"
              onPress={handleSubmit(data => {
                onSubmit(data);
              })}
            />
          </FormProvider>
        </View>
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignContent: 'center',
  },
  title: {
    fontSize: 24,
    color: '#344260',
    fontFamily: 'Outfit-Bold.ttf',
    fontWeight: 'bold',
    marginVertical: 20,
  },
  subtitle: {
    marginBottom: 10,
    textAlign: 'center',
    fontSize: 14,
    color: '#344260',
    fontFamily: 'Outfit-Bold.ttf',
    fontWeight: 'bold',
  },
  text: {
    marginBottom: 40,
    textAlign: 'center',
    fontSize: 12,
    color: '#344260',
    fontFamily: 'Outfit-Regular.ttf',
    fontWeight: 'regular',
  },
  form: {
    width: '100%',
    padding: 20,
    backgroundColor: '#fff',
    flexDirection: 'column',
    marginBottom: 50,
  },
  navigation: {
    width: '100%',
    flexDirection: 'row',
    justifyContent: 'space-evenly',
  },
  navigationText: {
    fontFamily: 'Rubik-Bold.ttf',
    fontWeight: 'bold',
    marginBottom: 20,
    color: '#5B6880',
  },
});

export default Profile;
