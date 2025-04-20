import React from "react";
import { View, Text, StyleSheet } from "react-native";
import {
  getStatusColor,
  getStatusInterventionCard,
} from "@/app/utils/intervention";

interface EquipmentCardHomeProps {
  equipment: any[];
}

export const EquipmentCardHome: React.FC<EquipmentCardHomeProps> = ({
  equipment,
}) => {
  if (!equipment || equipment.length === 0) {
    return null;
  }

  return (
    <View style={styles.equipmentCard}>
      <Text style={styles.titleSide}>Vos équipements informatique</Text>
      <View style={styles.equipmentCardContainer}>
        {equipment.slice(0, 4).map((e: any, index: number) => {
          const status =
            e.interventions && e.interventions.length > 0
              ? getStatusInterventionCard(e.interventions)
              : "No data";
          const statusColor = getStatusColor(status);

          return (
            <View style={styles.card} key={index}>
              <Text style={styles.name}>{e.name}</Text>
              {e.interventions && e.interventions.length > 0 && (
                <Text style={[styles.status, { color: statusColor }]}>
                  {status}
                </Text>
              )}
            </View>
          );
        })}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  equipmentCard: {
    backgroundColor: "#F5F5F8",
    borderRadius: 20,
    padding: 16,
    marginBottom: 20,
    width: "90%",
    alignSelf: "center",
  },
  equipmentCardContainer: {
    width: "100%",
    paddingHorizontal: 16,
  },
  card: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderRadius: 10,
    padding: 16,
    marginBottom: 10,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  titleSide: {
    color: "#637381",
    fontWeight: "bold",
    fontSize: 18,
    textTransform: "uppercase",
    marginBottom: 16,
  },
  name: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#364A63",
  },
  status: {
    fontSize: 16,
    fontWeight: "600",
  },
  noIntervention: {
    fontSize: 14,
    color: "#999",
  },
});
