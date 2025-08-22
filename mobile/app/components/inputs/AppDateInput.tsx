import React, { useState } from 'react';
import { Platform, View, Text, StyleSheet, Button } from 'react-native';
import { Controller, useFormContext } from 'react-hook-form';
import DateTimePicker from '@react-native-community/datetimepicker';

interface AppDateInputProps {
  nameField: string;
  label?: string;
  defaultValue?: Date;
  placeholder?: string;
  rules?: object;
  formatDate?: (date: Date) => string; // Ajout du prop pour formater la date
}

export const AppDateInput: React.FC<AppDateInputProps> = ({
  nameField,
  label,
  defaultValue = new Date(),
  placeholder,
  rules = {},
  formatDate = date => date.toISOString().split('T')[0], // Format par défaut : YYYY-MM-DD
}: AppDateInputProps) => {
  const { control } = useFormContext();
  const [showPicker, setShowPicker] = useState(false);

  return (
    <Controller
      control={control}
      name={nameField}
      defaultValue={defaultValue}
      rules={rules}
      render={({ field: { onChange, value }, fieldState: { error } }) => (
        <View style={styles.container}>
          {label && <Text style={styles.label}>{label}</Text>}
          <Button
            title={
              value
                ? formatDate(new Date(value)) // Utilise la fonction de formatage
                : placeholder || 'Sélectionner une date'
            }
            onPress={() => setShowPicker(true)}
          />
          {showPicker && (
            <DateTimePicker
              value={value ? new Date(value) : defaultValue}
              mode="date"
              display={Platform.OS === 'ios' ? 'inline' : 'default'}
              onChange={(event, selectedDate) => {
                setShowPicker(false);
                if (selectedDate) {
                  onChange(selectedDate);
                }
              }}
            />
          )}
          {error && <Text style={styles.errorText}>{error.message}</Text>}
        </View>
      )}
    />
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: 16,
  },
  label: {
    fontSize: 14,
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#364A63',
  },
  errorText: {
    color: 'red',
    fontSize: 12,
    marginTop: 4,
  },
});
