import React from "react";
import { View, Text, StyleSheet } from "react-native";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { useSessionContext } from "@/app/context/useSessionContext";
import { FormProvider, useForm } from "react-hook-form";
import { useApi } from "@/app/utils/useApi";
import { AppButton } from "@/app/components/buttons/AppButton";

const Profile = () => {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const methods = useForm();
  const { handleSubmit } = methods;
  const api = useApi();

  const onSubmit = async (data: any) => {
    try {
      const response = await api.put(`/api/profile`, data);
      if (response.status !== 200) {
        return;
      }
    } catch (error) {
      console.error("Error updating profile:", error);
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.navigation}>
        <Text style={styles.navigationText}>Compte</Text>
        <Text style={styles.navigationText}>Adresse</Text>
      </View>
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
                message: "Adresse email invalide",
              },
            }}
            value={sessionData?.email}
          />
          <AppTextField
            nameField="Nom"
            label="Nom"
            placeholder="Entrez votre nom"
            value={sessionData?.last_name}
            rules={{ required: "Le nom est obligatoire" }}
          />
          <AppTextField
            nameField="Prénom"
            label="Prénom"
            placeholder="Entrez votre prénom"
            value={sessionData?.first_name}
            rules={{ required: "Le prénom est obligatoire" }}
          />
          <AppTextField
            nameField="Téléphone"
            label="Téléphone"
            placeholder="Entrez votre numéro de téléphone"
            rules={{
              required: "Le numéro de téléphone est obligatoire",
              pattern: {
                value: /^\d{10}$/,
                message: "Numéro de téléphone invalide",
              },
            }}
            value={"a changer"}
          />
          <AppTextField
            nameField="password"
            label="Mot de passe"
            placeholder="Entrez votre mot de passe"
            secureTextEntry
            rules={{ required: "Le mot de passe est obligatoire" }}
            value={""}
          />
          <AppButton
            type="primary"
            icon={"chevron-right"}
            children="Mettre à jour"
            onPress={handleSubmit((data) => {
              onSubmit(data);
            })}
          />
        </FormProvider>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#F0F3F4",
  },
  title: {
    fontSize: 24,
    color: "#344260",
    fontFamily: "Outfit-Bold.ttf",
    fontWeight: "bold",
    marginVertical: 20,
  },
  subtitle: {
    marginBottom: 10,
    textAlign: "center",
    fontSize: 14,
    color: "#344260",
    fontFamily: "Outfit-Bold.ttf",
    fontWeight: "bold",
  },
  text: {
    marginBottom: 40,
    textAlign: "center",
    fontSize: 12,
    color: "#344260",
    fontFamily: "Outfit-Regular.ttf",
    fontWeight: "regular",
  },
  form: {
    width: "100%",
    padding: 20,
    backgroundColor: "#fff",
    flexDirection: "column",
  },
  navigation: {
    width: "100%",
    flexDirection: "row",
    justifyContent: "space-evenly",
  },
  navigationText: {
    fontFamily: "Rubik-Bold.ttf",
    fontWeight: "bold",
    marginBottom: 20,
    color: "#5B6880",
  },
});

export default Profile;
