import React, { useState } from "react";
import { View, Text, TextInput, Button, StyleSheet } from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { useSession } from "./context/ctx";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { signIn, session } = useSession();
  const router = useRouter();
  const params = useLocalSearchParams();
  console.log(params);

  const handleLogin = async () => {
    const success = await signIn(email, password);
    if (success) {
      console.log(session);
      router.replace("/(main)/customer");
    } else {
      alert("Login failed. Please try again.");
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Login</Text>
      <TextInput
        style={styles.input}
        placeholder="Email"
        value={email}
        onChangeText={setEmail}
      />
      <TextInput
        style={styles.input}
        placeholder="Password"
        secureTextEntry
        value={password}
        onChangeText={setPassword}
      />
      {params.type === "customer" ? (
        <Button title="Connexion" onPress={handleLogin} />
      ) : (
        <Button title="Login" onPress={handleLogin} />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: "center", padding: 16 },
  title: { fontSize: 24, marginBottom: 16 },
  input: { borderWidth: 1, padding: 8, marginBottom: 16 },
});
