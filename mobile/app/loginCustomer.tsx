import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet } from "react-native";
import { useRouter } from "expo-router";
import { LoginForm } from "./components/auth/LoginForm";
import { Divider } from "react-native-paper";
import { ToolBarCustomer } from "./components/navigation/ToolBarCustomer";

export default function LoginCustomer() {
  const [success, setSuccess] = useState<boolean | null>(null);
  const router = useRouter();

  useEffect(() => {
    if (success === false) {
      return;
    }
    if (success) {
      router.replace("/(tabs)/customer");
    }
  }, [success]);

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title={"Espace Client"}
        bottomBar
        showBack
        onBackPress={() => router.push("/")}
      />
      <View style={styles.container}>
        <View style={styles.form}>
          <Text style={styles.title}>
            Connexion à votre espace client en ligne
          </Text>
          <Text style={styles.subtitle}>
            Pour vous connecter, utilisez votre adresse email fournie au
            technicien.
          </Text>
          {success === false && (
            <Text style={styles.errorMessage}>
              Mot de passe ou login incorrect
            </Text>
          )}
          <LoginForm success={success} setSuccess={setSuccess} />
          <Text style={styles.inline}>
            Votre accès est confidentiel, ne le communiquez jamais à autrui.
          </Text>
        </View>
        <View style={styles.containerBar}>
          <Divider />
        </View>
        <Text style={styles.inline}>
          Vous n'avez pas de compte ?{" "}
          <Text
            style={{ color: "#E53953", fontWeight: "bold" }}
            onPress={() => router.push("/registerCustomer")}
          >
            Inscrivez-vous
          </Text>
        </Text>
      </View>
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
    padding: 20,
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
  inline: {
    textAlign: "center",
    color: "#5B6880",
    fontSize: 14,
    marginVertical: 20,
  },
  errorMessage: {
    textAlign: "center",
    color: "#ff0000",
  },
  containerBar: {
    flexDirection: "row",
    justifyContent: "space-around",
    alignItems: "center",
    marginTop: 30,
  },
});
