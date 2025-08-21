import { useApi } from "@/app/hooks/useApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import { FC, useEffect, useState } from "react";
import {
  Text,
  View,
  ScrollView,
  StyleSheet,
  TextInput,
  Alert,
  TouchableOpacity,
  ActivityIndicator,
} from "react-native";
import React from "react";
import { Feather } from "@expo/vector-icons";
import { useForm, FormProvider } from "react-hook-form";
import { AppSelectInput } from "@/app/components/inputs/AppSelectInput";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

interface AppointmentData {
  id: number;
  date: string;
  title: string;
  description: string;
  type_intervention: string | null;
  equipment: {
    name: string;
    id: number;
  } | null;
  created_at: string;
  updated_at: string;
  status: string;
  company: {
    name: string;
    id: number;
  } | null;
  user: [number, string, string, string] | null; // [id, firstName, lastName, email]
}

const AdminAppointmentDetailsPage: FC = () => {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const api = useApi();

  const [appointment, setAppointment] = useState<AppointmentData | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [isEditing, setIsEditing] = useState(false);

  // États pour les champs modifiables
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");

  // Form pour le select
  const methods = useForm({
    defaultValues: {
      status: "",
    },
  });

  const statusOptions = [
    { label: "En attente", value: "pending" },
    { label: "Planifié", value: "scheduled" },
    { label: "Terminé", value: "completed" },
    { label: "Annulé", value: "cancelled" },
  ];

  useEffect(() => {
    loadAppointment();
  }, [id]);

  const loadAppointment = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/appointment?id=${id}`);
      if (response.data && response.data.length > 0) {
        const appointmentData = response.data[0];
        setAppointment(appointmentData);
        setTitle(appointmentData.title);
        setDescription(appointmentData.description);
        methods.setValue("status", appointmentData.status);
      }
    } catch (error) {
      console.error("Erreur lors du chargement du rendez-vous:", error);
      Alert.alert("Erreur", "Impossible de charger les détails du rendez-vous");
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!appointment) return;

    try {
      setSaving(true);
      const formData = methods.getValues();
      const payload = {
        title,
        description,
        status: formData.status,
        company_id: appointment.company?.id,
        date: appointment.date,
      };

      await api.put(`/appointment/${id}`, payload);

      Alert.alert("Succès", "Rendez-vous mis à jour avec succès", [
        {
          text: "OK",
          onPress: () => {
            setIsEditing(false);
            loadAppointment(); // Recharger les données
          },
        },
      ]);
    } catch (error) {
      console.error("Erreur lors de la sauvegarde:", error);
      Alert.alert("Erreur", "Impossible de sauvegarder les modifications");
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    if (appointment) {
      setTitle(appointment.title);
      setDescription(appointment.description);
      methods.setValue("status", appointment.status);
    }
    setIsEditing(false);
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("fr-FR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  };

  const getStatusColor = (statusValue: string) => {
    switch (statusValue) {
      case "pending":
        return "#FF9800";
      case "scheduled":
        return "#2196F3";
      case "completed":
        return "#4CAF50";
      case "cancelled":
        return "#F44336";
      default:
        return "#757575";
    }
  };

  const getUserName = () => {
    if (!appointment?.user) return "Client non défini";
    const [, firstName, lastName] = appointment.user;
    return `${firstName} ${lastName}`;
  };

  const getUserEmail = () => {
    if (!appointment?.user) return "";
    const [, , , email] = appointment.user;
    return email;
  };

  if (loading) {
    return (
      <View style={[styles.container, styles.centered]}>
        <ActivityIndicator size="large" color="#01358D" />
        <Text style={styles.loadingText}>Chargement...</Text>
      </View>
    );
  }

  if (!appointment) {
    return (
      <View style={[styles.container, styles.centered]}>
        <Text style={styles.errorText}>Rendez-vous introuvable</Text>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <ToolBarAdmin
        title="Détails du rendez-vous"
        bottomBar
        onBackPress={() => router.back()}
        showBack
        rightContent={
          <TouchableOpacity
            onPress={() => setIsEditing(!isEditing)}
            style={styles.editButton}
            disabled={appointment.status === "scheduled"}
          >
            <Feather
              name={isEditing ? "x" : "edit-2"}
              size={24}
              color={appointment.status === "scheduled" ? "#ccc" : "#01358D"}
            />
          </TouchableOpacity>
        }
      />
      {appointment.status === "scheduled" && (
        <View style={styles.warningBanner}>
          <Feather name="alert-triangle" size={16} color="#FF9800" />
          <Text style={styles.warningText}>
            Ce rendez-vous a été accepté et ne peut plus être modifié
          </Text>
        </View>
      )}

      <View style={styles.content}>
        {/* Informations client */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Client</Text>
          <View style={styles.clientCard}>
            <View style={styles.clientInfo}>
              <Feather name="user" size={20} color="#01358D" />
              <View style={styles.clientDetails}>
                <Text style={styles.clientName}>{getUserName()}</Text>
                <Text style={styles.clientEmail}>{getUserEmail()}</Text>
              </View>
            </View>
          </View>
        </View>

        {/* Informations équipement */}
        {appointment.equipment && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Équipement</Text>
            <View style={styles.equipmentCard}>
              <Feather name="monitor" size={20} color="#01358D" />
              <View style={styles.equipmentDetails}>
                <Text style={styles.equipmentName}>
                  {appointment.equipment.name}
                </Text>
                <Text style={styles.equipmentId}>
                  ID: {appointment.equipment.id}
                </Text>
              </View>
            </View>
          </View>
        )}

        {/* Détails du rendez-vous */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Détails du rendez-vous</Text>

          <View style={styles.infoRow}>
            <Feather name="calendar" size={16} color="#666" />
            <Text style={styles.infoLabel}>Date:</Text>
            <Text style={styles.infoValue}>{formatDate(appointment.date)}</Text>
          </View>

          <View style={styles.infoRow}>
            <Feather name="tag" size={16} color="#666" />
            <Text style={styles.infoLabel}>Statut:</Text>
            {isEditing ? (
              <View style={styles.selectContainer}>
                <FormProvider {...methods}>
                  <AppSelectInput
                    nameField="status"
                    placeholder="Sélectionnez un statut"
                    options={statusOptions}
                  />
                </FormProvider>
              </View>
            ) : (
              <View
                style={[
                  styles.statusBadge,
                  { backgroundColor: getStatusColor(appointment.status) },
                ]}
              >
                <Text style={styles.statusText}>
                  {statusOptions.find((opt) => opt.value === appointment.status)
                    ?.label || appointment.status}
                </Text>
              </View>
            )}
          </View>

          <View style={styles.fieldContainer}>
            <Text style={styles.fieldLabel}>Titre:</Text>
            {isEditing ? (
              <TextInput
                style={styles.textInput}
                value={title}
                onChangeText={setTitle}
                placeholder="Titre du rendez-vous"
              />
            ) : (
              <Text style={styles.fieldValue}>{appointment.title}</Text>
            )}
          </View>

          <View style={styles.fieldContainer}>
            <Text style={styles.fieldLabel}>Description:</Text>
            {isEditing ? (
              <TextInput
                style={[styles.textInput, styles.textArea]}
                value={description}
                onChangeText={setDescription}
                placeholder="Description du rendez-vous"
                multiline
                numberOfLines={4}
              />
            ) : (
              <Text style={styles.fieldValue}>
                {appointment.description || "Aucune description"}
              </Text>
            )}
          </View>

          {appointment.type_intervention && (
            <View style={styles.infoRow}>
              <Feather name="tool" size={16} color="#666" />
              <Text style={styles.infoLabel}>Type d'intervention:</Text>
              <Text style={styles.infoValue}>
                {appointment.type_intervention}
              </Text>
            </View>
          )}
        </View>

        {/* Boutons d'action */}
        {isEditing && (
          <View style={styles.actionButtons}>
            <TouchableOpacity
              style={[styles.button, styles.cancelButton]}
              onPress={handleCancel}
            >
              <Text style={styles.cancelButtonText}>Annuler</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.button, styles.saveButton]}
              onPress={handleSave}
              disabled={saving}
            >
              {saving ? (
                <ActivityIndicator size="small" color="#fff" />
              ) : (
                <Text style={styles.saveButtonText}>Sauvegarder</Text>
              )}
            </TouchableOpacity>
          </View>
        )}

        {/* Informations système */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Informations système</Text>
          <View style={styles.systemInfo}>
            <Text style={styles.systemText}>
              Créé le {formatDate(appointment.created_at)}
            </Text>
            {appointment.updated_at && (
              <Text style={styles.systemText}>
                Modifié le {formatDate(appointment.updated_at)}
              </Text>
            )}
            {appointment.company && (
              <Text style={styles.systemText}>
                Entreprise: {appointment.company.name}
              </Text>
            )}
          </View>
        </View>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5",
  },
  centered: {
    justifyContent: "center",
    alignItems: "center",
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: 16,
    backgroundColor: "#fff",
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
  },
  backButton: {
    padding: 8,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
  },
  editButton: {
    padding: 8,
  },
  warningBanner: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#FFF3CD",
    padding: 12,
    marginHorizontal: 16,
    marginTop: 16,
    borderRadius: 8,
    gap: 8,
  },
  warningText: {
    color: "#856404",
    fontSize: 14,
    flex: 1,
  },
  content: {
    padding: 16,
  },
  section: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 12,
  },
  clientCard: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  clientInfo: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
  },
  clientDetails: {
    flex: 1,
  },
  clientName: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
  },
  clientEmail: {
    fontSize: 14,
    color: "#666",
  },
  equipmentCard: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
  },
  equipmentDetails: {
    flex: 1,
  },
  equipmentName: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
  },
  equipmentId: {
    fontSize: 14,
    color: "#666",
  },
  infoRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 12,
    gap: 8,
  },
  infoLabel: {
    fontSize: 14,
    fontWeight: "500",
    color: "#333",
    minWidth: 60,
  },
  infoValue: {
    fontSize: 14,
    color: "#666",
    flex: 1,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  statusText: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "600",
  },
  fieldContainer: {
    marginBottom: 16,
  },
  fieldLabel: {
    fontSize: 14,
    fontWeight: "500",
    color: "#333",
    marginBottom: 8,
  },
  fieldValue: {
    fontSize: 14,
    color: "#666",
    lineHeight: 20,
  },
  textInput: {
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 8,
    padding: 12,
    fontSize: 14,
    backgroundColor: "#fff",
  },
  textArea: {
    height: 100,
    textAlignVertical: "top",
  },
  pickerContainer: {
    flex: 1,
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 8,
    backgroundColor: "#fff",
  },
  selectContainer: {
    flex: 1,
  },
  picker: {
    height: 40,
  },
  actionButtons: {
    flexDirection: "row",
    gap: 12,
    marginTop: 24,
  },
  button: {
    flex: 1,
    padding: 16,
    borderRadius: 8,
    alignItems: "center",
  },
  cancelButton: {
    backgroundColor: "#f5f5f5",
    borderWidth: 1,
    borderColor: "#ddd",
  },
  cancelButtonText: {
    color: "#666",
    fontWeight: "600",
  },
  saveButton: {
    backgroundColor: "#01358D",
  },
  saveButtonText: {
    color: "#fff",
    fontWeight: "600",
  },
  systemInfo: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  systemText: {
    fontSize: 12,
    color: "#999",
    marginBottom: 4,
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: "#666",
  },
  errorText: {
    fontSize: 16,
    color: "#F44336",
  },
});

export default AdminAppointmentDetailsPage;
