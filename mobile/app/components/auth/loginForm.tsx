import { View } from "react-native";
import { TextInput } from "react-native-paper";
import { AppButton } from "../Buttons/AppButton";
import React, { useState } from "react";
import { useSession } from "@/app/context/ctx";

interface LoginFormProps {
  success: boolean | null;
  setSuccess: (value: boolean) => void;
}

export const LoginForm: React.FC<LoginFormProps> = ({
  success,
  setSuccess,
}: LoginFormProps) => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { signIn } = useSession();

  const handleLogin = async () => {
    const success = await signIn(email, password);
    success ? setSuccess(true) : setSuccess(false);
  };
  return (
    <View>
      <TextInput
        mode="outlined"
        label="Email"
        placeholder="Entrez votre identifiant ou adresse email"
        value={email}
        onChangeText={(text) => setEmail(text)}
      />
      <TextInput
        mode="outlined"
        label="Mot de passe"
        placeholder="Entrez votre mot de passe"
        secureTextEntry
        right={<TextInput.Icon icon="eye" />}
        value={password}
        onChangeText={(text) => setPassword(text)}
      />
      <AppButton
        children={"Connexion"}
        type="validate"
        onPress={() => handleLogin()}
        disabled={false}
        icon="login"
      />
    </View>
  );
};
