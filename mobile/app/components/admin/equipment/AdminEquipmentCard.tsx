import React, { FC } from "react";
import { View, Text, StyleSheet, Pressable } from "react-native";

interface Equipment {
  id: number;
  name: string;
  reference?: string;
  model?: string;
  created_at: string;
  updated_at: string;
  user?: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
  };
  brand?: {
    id: number;
    name: string;
  };
  type_equipment?: {
    id: number;
    name: string;
  };
  operating_system?: {
    id: number;
    name: string;
  };
}

interface AdminEquipmentCardProps {
  equipment: Equipment;
  onPress: () => void;
}

export const AdminEquipmentCard: FC<AdminEquipmentCardProps> = ({
  equipment,
  onPress,
}) => {
  // Formatter la date d'ajout
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  return (
    <Pressable style={styles.card} onPress={onPress}>
      <View style={styles.header}>
        <Text style={styles.name}>{equipment.name}</Text>
        <Text style={styles.date}>
          Ajouté le {formatDate(equipment.created_at)}
        </Text>
      </View>

      <View style={styles.content}>
        {equipment.brand && (
          <Text style={styles.brand}>
            {equipment.brand.name}
          </Text>
        )}

        {equipment.reference && (
          <Text style={styles.reference}>
            Réf: {equipment.reference}
          </Text>
        )}

        {equipment.model && (
          <Text style={styles.model}>
            Modèle: {equipment.model}
          </Text>
        )}

        {equipment.type_equipment && (
          <Text style={styles.type}>
            Type: {equipment.type_equipment.name}
          </Text>
        )}

        {equipment.operating_system && (
          <Text style={styles.os}>
            OS: {equipment.operating_system.name}
          </Text>
        )}
      </View>

      {equipment.user && (
        <View style={styles.footer}>
          <Text style={styles.owner}>
            Propriétaire: {equipment.user.first_name} {equipment.user.last_name}
          </Text>
          <Text style={styles.ownerEmail}>
            {equipment.user.email}
          </Text>
        </View>
      )}
    </Pressable>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    marginHorizontal: 16,
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 5,
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  name: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    flex: 1,
  },
  date: {
    fontSize: 12,
    color: "#666",
  },
  content: {
    marginBottom: 12,
  },
  brand: {
    fontSize: 16,
    color: "#F9556D",
    fontWeight: "600",
    marginBottom: 4,
  },
  reference: {
    fontSize: 14,
    color: "#555",
    marginBottom: 2,
  },
  model: {
    fontSize: 14,
    color: "#555",
    marginBottom: 2,
  },
  type: {
    fontSize: 14,
    color: "#007AFF",
    marginBottom: 2,
  },
  os: {
    fontSize: 14,
    color: "#34C759",
    marginBottom: 2,
  },
  footer: {
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: "#eee",
  },
  owner: {
    fontSize: 14,
    fontWeight: "600",
    color: "#333",
    marginBottom: 2,
  },
  ownerEmail: {
    fontSize: 12,
    color: "#666",
  },
});
