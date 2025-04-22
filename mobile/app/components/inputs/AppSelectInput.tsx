import React from "react";
import { View, Text, StyleSheet } from "react-native";
import { Picker } from "@react-native-picker/picker";
import { Controller, useFormContext } from "react-hook-form";

interface AppSelectInputProps {
  nameField: string;
  label?: string;
  placeholder?: string;
  options: { label: string; value: string | number }[];
  rules?: object;
  defaultValue?: string | number | null;
}

export const AppSelectInput: React.FC<AppSelectInputProps> = ({
  nameField,
  label,
  placeholder,
  options,
  rules,
  defaultValue = null,
}) => {
  const { control } = useFormContext();

  return (
    <View style={styles.container}>
      {label && <Text style={styles.label}>{label}</Text>}
      <Controller
        control={control}
        name={nameField}
        defaultValue={defaultValue}
        rules={rules}
        render={({ field: { onChange, value }, fieldState: { error } }) => (
          <>
            <View style={styles.pickerContainer}>
              <Picker
                selectedValue={value}
                onValueChange={(itemValue) => onChange(parseInt(itemValue))}
                style={styles.picker}
              >
                <Picker.Item label={placeholder || "Sélectionnez une option"} value="" />
                {options.map((option) => (
                  <Picker.Item
                    key={option.value}
                    label={option.label}
                    value={option.value}
                  />
                ))}
              </Picker>
            </View>
            {error && <Text style={styles.errorText}>{error.message}</Text>}
          </>
        )}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    fontWeight: "bold",
    marginBottom: 8,
    color: "#344260",
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: "#ccc",
    borderRadius: 8,
    overflow: "hidden",
    backgroundColor: "#fff",
  },
  picker: {
    height: 50,
  },
  errorText: {
    marginTop: 4,
    fontSize: 12,
    color: "red",
  },
});