import { useLocalSearchParams } from "expo-router";
import { View } from "react-native";
import React from "react";
import { useApi } from "@/app/utils/useApi";

export default function InterventionDetailPage() {
  const { id } = useLocalSearchParams();
  const [intervention, setIntervention] = React.useState<any>(null);
  const api = useApi();
  return <View></View>;
}
