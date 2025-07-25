import { View, StyleSheet } from "react-native";
import React from "react";
import { useSessionContext } from "@/app/context/useSessionContext";
import { Divider, List, Text } from "react-native-paper";
import { router } from "expo-router";
import { AppButton } from "@/app/components/buttons/AppButton";
import { useSession } from "@/app/context/ctx";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

export const ProfilePage = () => {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const { signOut } = useSession();

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Paramétres"
        showBack
        onBackPress={() => router.push("/customer")}
        bottomBar
      />
      <View style={styles.container}>
        <Text variant="titleLarge" style={styles.textTitle}>
          {sessionData?.first_name} {sessionData?.last_name}
        </Text>
        <List.Item
          title="Mes coordonnées de contact"
          left={(props) => <List.Icon {...props} icon="folder" />}
          onPress={() => router.push("/customer/settings/profile")}
          style={styles.subtitle}
        />
        <Divider />
        <List.Item
          title="A propos de l'application"
          left={(props) => <List.Icon {...props} icon="folder" />}
          style={styles.subtitle}
        />
        <Divider />
        <View style={styles.button}>
          <AppButton
            children="Me déconnecter"
            type="secondary"
            onPress={() => signOut()}
          />
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  textTitle: {
    color: "#364A63",
    fontSize: 25,
    fontWeight: "bold",
    marginBottom: 10,
    width: "90%",
    alignSelf: "center",
  },
  container: {
    flex: 1,
    width: "100%",
    padding: 20,
    backgroundColor: "#fff",
    flexDirection: "column",
    marginBottom: 50,
  },
  subtitle: {
    marginBottom: 10,
    textAlign: "center",
    fontSize: 14,
    color: "#344260",
    fontFamily: "Outfit-Bold.ttf",
    fontWeight: "bold",
  },
  button: {
    alignContent: "flex-end",
    justifyContent: "flex-end",
  },
});

export default ProfilePage;
