import React, { FC, useEffect, useState } from "react";
import { TouchableOpacity, View, Text, StyleSheet } from "react-native";
import { AppointmentModalForm } from "./AppointmentModalForm";
import { Card } from "react-native-paper";
import { APPOINTMENT_STATUS, getStatusAppointmentCard } from "@/app/utils/intervention";

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
;

  return (
    <Card onPress={onPress} style={styles.card}>
      <Card.Title title={title} titleStyle={styles.title} />
      <Card.Content>
        <Text style={styles.status}>{getStatusAppointmentCard(status)}</Text>
        <View style={styles.details}>
          <Text style={styles.date}>{formattedDate}</Text>
        </View>
      </Card.Content>
      <Card.Actions>
        {status === APPOINTMENT_STATUS.PENDING && (
          <AppointmentModalForm
            type="update"
            id={appointment.id}
            button={
              <TouchableOpacity>
                <Text style={styles.companyName}>Modifier</Text>
              </TouchableOpacity>
            }
          />
        )}
      </Card.Actions>
    </Card>
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
