import { useSession } from "@/app/context/ctx";
import React, { useState } from "react";
import { View, StyleSheet } from "react-native";
import { Text } from "react-native-paper";

export const CustomerHome = () => {
  const [user, setUser] = useState<any | null>(null);
  const { session } = useSession();

  console.log(session);
  return (
    <View style={styles.container}>
      <Text variant="displayMedium">Bienvenue</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
});

export default CustomerHome;
