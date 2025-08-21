import React, { useState } from "react";
import { View, StyleSheet, Alert, Text } from "react-native";
import { useForm, FormProvider } from "react-hook-form";
import { useRouter } from "expo-router";
import { useApi } from "../hooks/useApi";
import { AppTextField } from "../components/inputs/AppTextField";
import { AppButton } from "../components/buttons/AppButton";
import { ToolBarCustomer } from "@/app/components/customer/navigation/ToolBarCustomer";
import { Snackbar } from "react-native-paper";

export default function RegisterForm() {
  const [snackbarVisible, setSnackbarVisible] = useState<boolean>(false);
  const methods = useForm();
  const { handleSubmit } = methods;
  const router = useRouter();
  const api = useApi();

  const onSubmit = async (data: any) => {
    try {
      const response = await api.post(`/auth/customer/register`, data);
      if (response.status !== 201) {
        Alert.alert("Erreur", "Problème lors de l'inscription.");
        return;
      }
      setSnackbarVisible(true);
      setTimeout(() => {
        setSnackbarVisible(false);
        router.push("/loginCustomer");
      }, 3000);
      
    } catch (error) {
      Alert.alert("Erreur", "Problème lors de l'inscription.");
    }
  };

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title={"Inscription Client"}
        bottomBar
        showBack
        onBackPress={() => router.push("/loginCustomer")}
      />
      <FormProvider {...methods}>
        <View style={styles.container}>
          <View style={styles.form}>
            <Text style={styles.title}>Créer votre espace client</Text>
            <Text style={styles.subtitle}>
              Remplissez le formulaire pour créer votre compte client.
            </Text>
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
      <Snackbar
        visible={snackbarVisible}
        onDismiss={() => setSnackbarVisible(false)}
        duration={3000}
      >
        Inscription réussie ! Vous pouvez maintenant vous connecter.
      </Snackbar>
    </View>
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
    marginBottom: 10,
  },
  subtitle: {
    textAlign: "center",
    fontSize: 16,
    marginTop: 20,
    marginBottom: 40,
  },
});
