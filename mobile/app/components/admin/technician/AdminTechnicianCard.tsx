import React, { FC } from "react";
import { View, Text, StyleSheet, Pressable } from "react-native";

interface Technician {
  id: number;
  email: string;
  role: string;
  first_name: string;
  last_name: string;
  created_at: string;
  updated_at: string;
  company?: any;
  zipcode: string;
  city: string;
  phone: string;
  address: string;
}

interface AdminTechnicianCardProps {
  technician: Technician;
  onPress: () => void;
}

export const AdminTechnicianCard: FC<AdminTechnicianCardProps> = ({
  technician,
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

  // Formatter le rôle
  const formatRole = (role: string) => {
    switch (role) {
      case "ROLE_TECHNICIAN":
        return "Technicien";
      case "ROLE_ADMIN":
        return "Admin";
      default:
        return role;
    }
  };

  return (
    <Pressable style={styles.card} onPress={onPress}>
      <View style={styles.header}>
        <Text style={styles.name}>
          {technician.first_name} {technician.last_name}
        </Text>
        <Text style={styles.date}>
          Ajouté le {formatDate(technician.created_at)}
        </Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.role}>
          {formatRole(technician.role)}
        </Text>

        <Text style={styles.location}>
          {technician.zipcode} - {technician.city}
        </Text>

        <Text style={styles.email}>{technician.email}</Text>

        {technician.phone && <Text style={styles.phone}>{technician.phone}</Text>}
      </View>
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
  },
  date: {
    fontSize: 12,
    color: "#666",
  },
  content: {
    marginBottom: 12,
  },
  role: {
    fontSize: 14,
    color: "#F9556D",
    fontWeight: "bold",
    marginBottom: 4,
  },
  location: {
    fontSize: 14,
    color: "#555",
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
    color: "#007AFF",
    marginBottom: 4,
  },
  phone: {
    fontSize: 14,
    color: "#555",
  },
});
