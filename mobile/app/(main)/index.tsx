import React, { useState, useCallback } from "react";
import { View, Text, Image, StyleSheet } from "react-native";
import { useRouter } from "expo-router";
// import logo from '../assets/images/logo.png';
import RedButtonHome from "@/app/components/Buttons/RedButtonHome";
import BlueButtonHome from "@/app/components/Buttons/BlueButtonHome";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { useFocusEffect } from "@react-navigation/native";

export default function HomeScreen() {
  const router = useRouter();
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useFocusEffect(
    useCallback(() => {
      const checkLoginStatus = async () => {
        try {
          const token = await AsyncStorage.getItem("userToken");
          setIsLoggedIn(!!token);
        } catch (error) {
          console.error("Error checking login status:", error);
          setIsLoggedIn(false);
        }
      };
      checkLoginStatus();
      return () => {};
    }, [])
  );

  const handleLogout = async () => {
    try {
      await AsyncStorage.removeItem("userToken");
      setIsLoggedIn(false);
      router.push("/");
    } catch (error) {
      console.error("Error logging out:", error);
    }
  };

  return (
    <View style={styles.container}>
      {/* <Image source={logo} style={{ marginBottom: 30 }} /> */}

      <Text style={styles.heading}>
        {"Votre "}
        <Text style={{ color: colors.secondary500 }}>App</Text>
        {"\n"}
        {"d'intervention en ligne"}
      </Text>

      {/* Profile and Logout Buttons */}
      {isLoggedIn && (
        <>
          <BlueButtonHome onPress={() => router.push("/(main)/Profile")}>
            Profil
          </BlueButtonHome>

          <RedButtonHome onPress={handleLogout}>Déconnexion</RedButtonHome>
        </>
      )}

      {/* Registration and Login Buttons */}
      {!isLoggedIn && (
        <>
          <RedButtonHome onPress={() => router.push("/(main)/auth/customer/login/Login")}>
            Espace Client
          </RedButtonHome>

          <BlueButtonHome onPress={() => router.push("/(main)/auth/admin/LoginTechnicien")}>
            Espace Technicien
          </BlueButtonHome>
        </>
      )}
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
