import React, { FC, useState, useEffect } from "react";
import { View, Text, StyleSheet, ScrollView, TouchableOpacity } from "react-native";
import { format, addDays, startOfDay, isSameDay } from "date-fns";
import { fr } from "date-fns/locale";
import { useApi } from "@/app/utils/useApi";

interface TimeSlotPickerProps {
  companyId?: number;
  onSlotSelect: (date: Date, startTime: string, endTime: string) => void;
  selectedDate?: Date;
  selectedTime?: string;
}

export const TimeSlotPicker: FC<TimeSlotPickerProps> = ({
  companyId,
  onSlotSelect,
  selectedDate,
  selectedTime,
}) => {
  const [selectedDay, setSelectedDay] = useState<Date>(selectedDate || new Date());
  const [availableSlots, setAvailableSlots] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const api = useApi();

  const next7Days = Array.from({ length: 7 }, (_, i) => addDays(new Date(), i));

  useEffect(() => {
    if (companyId && selectedDay) {
        console.log("test");
      fetchAvailableSlots();
    }
  }, [companyId, selectedDay]);

  const fetchAvailableSlots = async () => {
    if (!companyId) return;
    
    setLoading(true);
    try {
      const formatDate = format(selectedDay, "yyyy-MM-dd");
      const response = await api.get(
        `/appointment/free-slots?company_id=${companyId}&date=${formatDate}&interval=15`
      );
      setAvailableSlots(response.data || []);
    } catch (error) {
      console.error("Error fetching time slots:", error);
      setAvailableSlots([]);
    } finally {
      setLoading(false);
    }
  };

  const handleDaySelect = (day: Date) => {
    setSelectedDay(day);
  };

  const handleSlotSelect = (slot: any) => {
    const selectedDateTime = new Date(selectedDay);
    const [hours, minutes] = slot.start_time.split(':');
    selectedDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
    
    onSlotSelect(selectedDateTime, slot.start_time, slot.end_time);
  };

  const isSlotSelected = (slot: any) => {
    return selectedTime === slot.start_time && 
           selectedDate && 
           isSameDay(selectedDate, selectedDay);
  };

  return (
    <View style={styles.container}>
      {/* Calendrier horizontal - 7 prochains jours */}
      <Text style={styles.sectionTitle}>Choisir une date</Text>
      <ScrollView 
        horizontal 
        showsHorizontalScrollIndicator={false}
        style={styles.daysContainer}
        contentContainerStyle={styles.daysContent}
      >
        {next7Days.map((day, index) => {
          const isSelected = isSameDay(day, selectedDay);
          const isToday = isSameDay(day, new Date());
          
          return (
            <TouchableOpacity
              key={index}
              style={[
                styles.dayButton,
                isSelected && styles.dayButtonSelected,
                isToday && styles.dayButtonToday,
              ]}
              onPress={() => handleDaySelect(day)}
            >
              <Text style={[
                styles.dayText,
                isSelected && styles.dayTextSelected,
                isToday && styles.dayTextToday,
              ]}>
                {format(day, 'EEE', { locale: fr })}
              </Text>
              <Text style={[
                styles.dateText,
                isSelected && styles.dateTextSelected,
                isToday && styles.dateTextToday,
              ]}>
                {format(day, 'd')}
              </Text>
              {isToday && (
                <Text style={styles.todayLabel}>Aujourd'hui</Text>
              )}
            </TouchableOpacity>
          );
        })}
      </ScrollView>

      {/* Créneaux disponibles */}
      <Text style={styles.sectionTitle}>
        Créneaux disponibles - {format(selectedDay, 'EEEE d MMMM', { locale: fr })}
      </Text>
      
      {loading ? (
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>Chargement des créneaux...</Text>
        </View>
      ) : availableSlots.length > 0 ? (
        <ScrollView style={styles.slotsContainer}>
          <View style={styles.slotsGrid}>
            {availableSlots.map((slot, index) => (
              <TouchableOpacity
                key={index}
                style={[
                  styles.slotButton,
                  isSlotSelected(slot) && styles.slotButtonSelected,
                ]}
                onPress={() => handleSlotSelect(slot)}
              >
                <Text style={[
                  styles.slotText,
                  isSlotSelected(slot) && styles.slotTextSelected,
                ]}>
                  {slot.start_time.slice(0, 5)} - {slot.end_time.slice(0, 5)}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </ScrollView>
      ) : (
        <View style={styles.noSlotsContainer}>
          <Text style={styles.noSlotsText}>
            Aucun créneau disponible pour cette date
          </Text>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginVertical: 16,
    justifyContent: "center"
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "600",
    color: "#344260",
    marginBottom: 12,
  },
  daysContainer: {
    marginBottom: 24,
  },
  daysContent: {
    paddingHorizontal: 4,
    gap: 8,
  },
  dayButton: {
    alignItems: "center",
    padding: 12,
    borderRadius: 12,
    backgroundColor: "#f5f5f5",
    minWidth: 70,
    marginHorizontal: 4,
  },
  dayButtonSelected: {
    backgroundColor: "#007AFF",
  },
  dayButtonToday: {
    borderWidth: 2,
    borderColor: "#007AFF",
  },
  dayText: {
    fontSize: 12,
    fontWeight: "500",
    color: "#666",
    textTransform: "capitalize",
  },
  dayTextSelected: {
    color: "#fff",
  },
  dayTextToday: {
    color: "#007AFF",
  },
  dateText: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginTop: 4,
  },
  dateTextSelected: {
    color: "#fff",
  },
  dateTextToday: {
    color: "#007AFF",
  },
  todayLabel: {
    fontSize: 10,
    color: "#007AFF",
    marginTop: 2,
    fontWeight: "500",
  },
  slotsContainer: {
    maxHeight: 200,
  },
  slotsGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
  },
  slotButton: {
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    backgroundColor: "#f8f9fa",
    borderWidth: 1,
    borderColor: "#e9ecef",
    minWidth: 120,
    alignItems: "center",
  },
  slotButtonSelected: {
    backgroundColor: "#007AFF",
    borderColor: "#007AFF",
  },
  slotText: {
    fontSize: 14,
    fontWeight: "500",
    color: "#495057",
  },
  slotTextSelected: {
    color: "#fff",
  },
  loadingContainer: {
    padding: 32,
    alignItems: "center",
  },
  loadingText: {
    color: "#666",
    fontSize: 14,
  },
  noSlotsContainer: {
    padding: 32,
    alignItems: "center",
  },
  noSlotsText: {
    color: "#666",
    fontSize: 14,
    textAlign: "center",
  },
});
