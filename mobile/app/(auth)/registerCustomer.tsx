import React from "react";
import { View, StyleSheet, Alert } from "react-native";
import { Text } from "react-native-paper";
import { useForm, FormProvider } from "react-hook-form";
import { useRouter } from "expo-router";
import { useApi } from "../utils/useApi";
import { AppTextField } from "../components/inputs/AppTextField";
import { AppButton } from "../components/buttons/AppButton";

export default function RegisterForm() {
  const methods = useForm();
  const { handleSubmit } = methods;
  const router = useRouter();
  const api = useApi();

  const onSubmit = async (data: any) => {
    try {
      const response = await api.post(`/auth/customer/register`, data);
      if (response.status !== 200) {
        Alert.alert("Erreur", "Problème lors de l'inscription.");
        return;
      }
      router.push('/loginCustomer');
    } catch (error) {
      Alert.alert("Erreur", "Problème lors de l'inscription.");
    }
  };

  return (
    <FormProvider {...methods}>
      <View style={styles.container}>
        <View style={styles.form}>
          <Text style={styles.title}>Créer votre espace client</Text>

          <AppTextField
            nameField="email"
            label="Adresse email"
            placeholder="Entrez votre adresse email"
            rules={{
              required: "L'adresse email est obligatoire",
              pattern: {
                value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
                message: "Adresse email invalide",
              },
            }}
          />

          <AppTextField
            nameField="first_name"
            label="Prénom"
            placeholder="Entrez votre prénom"
            rules={{ required: "Le prénom est obligatoire" }}
          />

          <AppTextField
            nameField="last_name"
            label="Nom"
            placeholder="Entrez votre nom"
            rules={{ required: "Le nom est obligatoire" }}
          />

          <AppTextField
            nameField="password"
            label="Mot de passe"
            placeholder="Entrez votre mot de passe"
            secureTextEntry
            rules={{ required: "Le mot de passe est obligatoire" }}
          />

          <AppButton
            type="primary"
            icon={"chevron-right"}
            children="S'inscrire"
            onPress={handleSubmit((data) => {
              console.log("Form submitted with data:", data);
              onSubmit(data);
            })}
          />
        </View>
      </View>
    </FormProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#F0F3F4",
  },
  form: {
    width: "90%",
    padding: 15,
    backgroundColor: "#fff",
    borderRadius: 8,
    flexDirection: "column",
  },
  title: {
    textAlign: "center",
    fontSize: 20,
    fontWeight: "bold",
    marginBottom: 20,
  },
});