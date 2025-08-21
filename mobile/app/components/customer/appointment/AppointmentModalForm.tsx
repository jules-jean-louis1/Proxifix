import React, { FC, useEffect, useState } from "react";
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
} from "react-native";
import { AppButton } from "../../buttons/AppButton";
import { FormProvider, useForm } from "react-hook-form";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/hooks/useApi";
import { AppSelectInput } from "../../inputs/AppSelectInput";
import { Feather } from "@expo/vector-icons";
import { AppTextField } from "../../inputs/AppTextField";
import { ActivityIndicator, MD2Colors } from "react-native-paper";
import { APPOINTMENT_STATUS } from "@/app/utils/intervention";
import { CreateEquipmentModal } from "@/app/components/customer/equipment/CreateEquipmentModal";
import { TimeSlotPicker } from "./TimeSlotPicker";

interface AppointmentModalFormProps {
  mode: "create" | "update";
  id?: any;
  button?: React.ReactElement;
  onSuccess?: () => void;
}

export const AppointmentModalForm: FC<AppointmentModalFormProps> = ({
  mode,
  id,
  button,
  onSuccess = () => {},
}) => {
  const [modalVisible, setModalVisible] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [equipments, setEquipments] = useState<any[]>([]);
  const [companies, setCompanies] = useState<any[]>([]);
  const [appointment, setAppointment] = useState<any>();
  const [selectedDateTime, setSelectedDateTime] = useState<Date | null>(null);
  const [selectedTimeSlot, setSelectedTimeSlot] = useState<{
    start: string;
    end: string;
  } | null>(null);
  const methods = useForm({
    defaultValues: {
      equipment_id: "",
      company_id: "",
      title: "",
      description: "",
      date: "",
    },
  });
  const { handleSubmit } = methods;
  const sessionCtx = useSessionContext();
  const api = useApi();
  const sessionData = sessionCtx?.session;

  useEffect(() => {
    (async () => {
      if (!modalVisible) return;
      try {
        setIsLoading(true);
        const response = await api.get(`/equipment?user_id=${sessionData?.id}`);
        setEquipments(response.data);
        const companiesResponse = await api.get(`/company`);
        setCompanies(companiesResponse.data);
        if (id) {
          const resp = await api.get(`/appointment?id=${id}`);
          setAppointment(resp.data[0]);
        }
        setIsLoading(false);
      } catch (error) {
        console.error("Error fetching equipments or companies:", error);
      }
    })();
  }, [modalVisible]);

  useEffect(() => {
    if (appointment && mode === "update") {
      methods.setValue("equipment_id", appointment.equipment?.id || "");
      methods.setValue("company_id", appointment.company?.id || "");
      methods.setValue("title", appointment.title || "");
      methods.setValue("description", appointment.description || "");
      methods.setValue("date", appointment.date || "");

      // Initialiser les états pour le TimeSlotPicker
      if (appointment.date) {
        const appointmentDate = new Date(appointment.date);
        setSelectedDateTime(appointmentDate);

        const startTime = appointmentDate.toTimeString().slice(0, 8);

        const endDate = new Date(appointmentDate.getTime() + 60 * 60 * 1000);
        const endTime = endDate.toTimeString().slice(0, 8);

        setSelectedTimeSlot({ start: startTime, end: endTime });
      }
    }
  }, [appointment, mode, methods]);

  const onSubmit = async (data: any) => {
    try {
      // Utiliser la date et l'heure sélectionnées du TimeSlotPicker
      const submitData = {
        ...data,
        date: selectedDateTime ? selectedDateTime.toISOString() : data.date,
      };

      if (mode === "create") {
        await api.post("/appointment", submitData);
      } else if (
        mode === "update" &&
        appointment.status === APPOINTMENT_STATUS.PENDING
      ) {
        await api.put(`/appointment/${appointment?.id}`, submitData);
      }
      setModalVisible(false);
      onSuccess();
      methods.reset();
      setSelectedDateTime(null);
      setSelectedTimeSlot(null);
    } catch (error) {
      console.error("Error saving appointment:", error);
    }
  };

  if (modalVisible && isLoading) {
    return (
      <View style={{ padding: 20 }}>
        <ActivityIndicator animating={true} color={MD2Colors.red800} />
      </View>
    );
  }

  return (
    <View>
      {button &&
        React.cloneElement(button, {
          onPress: () => setModalVisible(true),
        })}

      <Modal
        animationType="slide"
        visible={modalVisible}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              style={{
                padding: 10,
                borderRadius: 50,
                backgroundColor: "#F0F3F4",
              }}
            >
              <Feather name="x" size={24} color={"#000"} />
            </TouchableOpacity>
            <Text style={styles.title}>
              {mode === "create"
                ? "Créer un rendez-vous"
                : "Modifier un rendez-vous"}
            </Text>
            <View style={{ width: 24 }}></View>
          </View>
          <ScrollView style={styles.modalContent}>
            <FormProvider {...methods}>
              <View style={styles.equipmentSection}>
                <Text style={styles.sectionTitle}>Équipement</Text>
                <View style={styles.equipmentRow}>
                  <View style={styles.equipmentSelect}>
                    <AppSelectInput
                      nameField="equipment_id"
                      label="Sélectionner un equipment"
                      options={equipments!.map((equipment: any) => ({
                        label: equipment.name,
                        value: equipment.id,
                      }))}
                      rules={{ required: "Ce champ est requis" }}
                    />
                  </View>
                  <CreateEquipmentModal
                    trigger={
                      <TouchableOpacity style={styles.iconButton}>
                        <Feather name="plus" size={20} color="#007AFF" />
                      </TouchableOpacity>
                    }
                    onSuccess={(newEquipment) => {
                      setEquipments((prev) => [...prev, newEquipment]);
                      methods.setValue("equipment_id", newEquipment.id);
                    }}
                  />
                </View>
              </View>
              <AppSelectInput
                nameField="company_id"
                label="Selectionner une entreprise"
                options={companies!.map((company: any) => ({
                  label: company.name,
                  value: company.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <TimeSlotPicker
                companyId={
                  mode === "create"
                    ? methods.watch("company_id")
                    : appointment?.company.id
                }
                onSlotSelect={(date, startTime, endTime) => {
                  setSelectedDateTime(date);
                  setSelectedTimeSlot({ start: startTime, end: endTime });
                  methods.setValue("date", date.toISOString());
                }}
                selectedDate={selectedDateTime || undefined}
                selectedTime={selectedTimeSlot?.start}
              />
              <AppTextField
                nameField="title"
                label="Titre"
                placeholder="Entrez le titre de l'intervention"
                rules={{ required: "Le titre est requis" }}
              />
              <AppTextField
                nameField="description"
                label="Description"
                placeholder="Entrez la description de l'intervention"
                multiline
                numberOfLines={3}
              />
              {appointment &&
                mode === "update" &&
                appointment.status === APPOINTMENT_STATUS.PENDING && (
                  <AppButton
                    type="secondary"
                    children="Supprimer"
                    onPress={async () => {
                      await api.delete(`/appointment/${id}`);
                      setModalVisible(false);
                      methods.reset();
                    }}
                  />
                )}
              {mode === "create" && (
                <AppButton
                  type="secondary"
                  children="Annuler"
                  onPress={() => {
                    setModalVisible(false);
                    methods.reset();
                    setSelectedDateTime(null);
                    setSelectedTimeSlot(null);
                  }}
                />
              )}
              <AppButton
                type="primary"
                children="Enregistrer"
                onPress={handleSubmit(onSubmit)}
              />
            </FormProvider>
          </ScrollView>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  modalContainer: {
    flex: 1,
    backgroundColor: "#fff",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
  title: {
    textAlign: "center",
    fontSize: 18,
    color: "#344260",
  },
  equipmentSection: {
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "600",
    color: "#344260",
    marginBottom: 8,
  },
  equipmentRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },
  equipmentSelect: {
    flex: 1,
  },
  iconButton: {
    padding: 8,
    borderRadius: 20,
    backgroundColor: "#F0F8FF",
    alignItems: "center",
    justifyContent: "center",
  },
});
