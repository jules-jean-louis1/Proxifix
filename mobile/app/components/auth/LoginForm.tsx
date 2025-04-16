import { AppButton } from "../buttons/AppButton";
import React from "react";
import { useSession } from "@/app/context/ctx";
import { FormProvider, useForm } from "react-hook-form";
import { AppTextField } from "../inputs/AppTextField";

interface LoginFormProps {
  success: boolean | null;
  setSuccess: (value: boolean) => void;
}

export const LoginForm: React.FC<LoginFormProps> = ({
  success,
  setSuccess,
}: LoginFormProps) => {
  const methods = useForm();
  const { handleSubmit } = methods;
  const { signIn } = useSession();

  const onSubmit = async (data: any) => {
    if (data.email === "" || data.password === "") {
      return;
    }
    const success = await signIn(data.email, data.password);
    success ? setSuccess(true) : setSuccess(false);
  };
  return (
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
      />
      <AppTextField
        nameField="password"
        label="Mot de passe"
        placeholder="Entrez votre mot de passe"
        secureTextEntry={true}
        rules={{ required: "Le mot de passe est obligatoire" }}
      />
      <AppButton
        children={"Connexion"}
        type="primary"
        onPress={handleSubmit((data) => {
          onSubmit(data);
        })}
        disabled={false}
        icon="login"
      />
    </FormProvider>
  );
};
