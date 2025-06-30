import React, { FC, useEffect, useState } from "react";
import { TouchableOpacity, View, Text, StyleSheet } from "react-native";

export const AppointmentCard: FC<{
  appointment: any;
  onPress?: () => void;
}> = ({ appointment, onPress }) => {
  const { company, date, title, status } = appointment;
  const formattedDate = new Date(date).toLocaleDateString("fr-FR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  return (
    <TouchableOpacity onPress={onPress} style={styles.card}>
      <View style={styles.header}>
        <Text style={styles.companyName}>{company.name}</Text>
        <Text style={styles.title}>{title}</Text>
        <Text style={styles.status}>{status}</Text>
      </View>
      <View style={styles.details}>
        <Text style={styles.date}>{formattedDate}</Text>
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#fff",
    width: "100%",
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
  },
  companyName: {
    fontSize: 16,
    fontWeight: "bold",
  },
  status: {
    fontSize: 14,
    color: "#888",
  },
  details: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  date: {
    fontSize: 14,
    color: "#333",
  },
  time: {
    fontSize: 14,
    color: "#333",
  },
  title: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#E53953",
  },
});
