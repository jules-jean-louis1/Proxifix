import React, { useState, useCallback } from "react";
import { View, Text, Image, StyleSheet } from "react-native";
import { useRouter } from "expo-router";
// import logo from '../assets/images/logo.png';
import RedButtonHome from "@/app/components/Buttons/RedButtonHome";
import BlueButtonHome from "@/app/components/Buttons/BlueButtonHome";

export default function HomeScreen() {
  const router = useRouter();

  return (
    <View style={styles.container}>
      {/* <Image source={logo} style={{ marginBottom: 30 }} /> */}

      <Text style={styles.heading}>
        {"Votre "}
        <Text style={{ color: colors.secondary500 }}>App</Text>
        {"\n"}
        {"d'intervention en ligne"}
      </Text>

      {/* Registration and Login Buttons */}

      <RedButtonHome
        onPress={() =>
          router.push({ pathname: "/Login", params: { type: "customer" } })
        }
      >
        Espace Client
      </RedButtonHome>

      <BlueButtonHome
        onPress={() => router.push({ pathname: "/Login", params: { type: "technician" } })}
      >
        Espace Technicien
      </BlueButtonHome>
    </View>
  );
}

const colors = {
  primary500: "#000000", // Add this if missing
  secondary500: "#E53953",
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#FFFFFF",
  },
  heading: {
    fontSize: 45,
    fontWeight: "bold",
    color: colors.primary500,
    fontFamily: "Rubik-Bold.ttf",
    marginBottom: 60,
    textAlign: "center",
  },
});
