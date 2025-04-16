import React from "react";
import { View, StyleSheet } from "react-native";
import { Slot } from "expo-router";

export default function CustomerLayout() {
  return (
    <View style={styles.container}>
      <Slot />
      {/*TODO: TabBarCustomer */}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "flex-start",
    backgroundColor: "#fff",
  },
});
