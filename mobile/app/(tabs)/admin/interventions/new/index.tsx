import { AppButton } from "@/app/components/buttons/AppButton";
import { AppSelectInput } from "@/app/components/inputs/AppSelectInput";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { Feather } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import { FormProvider, useForm } from "react-hook-form";
import { Pressable, ScrollView, View } from "react-native";
import { Text } from "react-native-paper";

type StepName = "detail" | "user" | "equipment" | "status";

type Step = {
  name: string;
  title: string;
};

const STEPS: Step[] = [
  { name: "detail", title: "Détails de l'intervention" },
  { name: "user", title: "Client et technicien" },
  { name: "equipment", title: "Equipement" },
  { name: "status", title: "Temps/Status" },
] as const;

const AdminInterventionsNewPage = () => {
  const [steps, setSteps] = useState([...STEPS]);
  const [activeStep, setActiveStep] = useState<StepName>("detail");
  const [typeIntervention, setTypeIntervention] = useState<any[]>([]);
  const [user, setUser] = useState<any[]>([]);
  const [companyUser, setCompanyUser] = useState<any[]>([]);
  const [equipment, setEquipment] = useState<any[]>([]);
  const [searchText, setSearchText] = useState("");

  const api = useApi();
  const methods = useForm();
  const { handleSubmit } = methods;
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;

  const isStepActive = (stepName: StepName) => activeStep === stepName;
  const nextStep = () => {
    const currentStepIndex = steps.findIndex(
      (step) => step.name === activeStep
    );
    if (currentStepIndex < steps.length - 1) {
      setActiveStep(steps[currentStepIndex + 1].name as StepName);
    }
  };
  const previousStep = () => {
    const currentStepIndex = steps.findIndex(
      (step) => step.name === activeStep
    );
    if (currentStepIndex > 0) {
      setActiveStep(steps[currentStepIndex - 1].name as StepName);
    }
  };

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(
          "/type-intervention?company_id=" + sessionData?.company.id
        );
        const userResponse = await api.get(`/user`);
        const companyUserResponse = await api.get(`/user?company_id=${sessionData?.company.id}`);
        setTypeIntervention(response.data);
        setUser(userResponse.data);
        setCompanyUser(companyUserResponse.data);
        console.log("user", user);
      } catch (error) {
        console.error("Error fetching type intervention:", error);
      }
    })();
  }, []);

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Nouvelle intervention"
        showBack
        onBackPress={() => router.push("/admin/interventions")}
        bottomBar
      />
      <ScrollView>
        <View>
          <Text variant="titleLarge">Nouvelle intervention</Text>
          <Text variant="bodyMedium">
            Veuillez renseigner les détails principaux de l'intervention
          </Text>
        </View>
        <FormProvider {...methods}>
          {isStepActive("detail") && (
            <View>
              <Text variant="titleLarge">Détails de l'intervention</Text>
              <Text variant="bodyMedium">Nom de l'intervention et détails</Text>
              <AppTextField
                nameField="title"
                label="Titre de l'intervention"
                rules={{ required: "Le titre de l'intervention est requis" }}
                placeholder="Entrez le titre de l'intervention"
              />
              <AppSelectInput
                nameField="type_intervention_id"
                label="Type d'intervention"
                options={typeIntervention.map((item) => ({
                  label: item.name,
                  value: item.id,
                }))}
                rules={{ required: "Le type d'intervention est requis" }}
                placeholder="Sélectionnez le type d'intervention"
              />
              <AppTextField
                nameField="description"
                label="Description de l'intervention"
                rules={{ required: "La description est requise" }}
                placeholder="Entrez la description de l'intervention"
                multiline
              />
              <AppButton type="primary" onPress={nextStep} children="Suivant" />
            </View>
          )}
          {isStepActive("user") && (
            <View>
              <Text variant="titleLarge">Client et technicien</Text>
              <Text variant="bodyMedium">
                Sélectionnez le client et le technicien
              </Text>
              <AppSelectInput
                nameField="user_id"
                label="Client"
                options={user!.map((item) => ({
                  label: `${item.first_name} ${item.last_name}`,
                  value: item.id,
                }))}
                rules={{ required: "Le client est requis" }}
                placeholder="Sélectionnez le client"
              />
              <AppButton
                type="secondary"
                onPress={previousStep}
                children="Précédent"
              />
              <AppButton type="primary" onPress={nextStep} children="Suivant" />
            </View>
          )}
        </FormProvider>
      </ScrollView>
    </View>
  );
};

export default AdminInterventionsNewPage;
