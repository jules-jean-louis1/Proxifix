import React from "react";
import { Controller, useFormContext } from "react-hook-form";
import { StyleSheet, Text } from "react-native";
import { TextInput } from "react-native-paper";

interface AppTextFieldProps {
  nameField: string;
  label: string;
  defaultValue?: string;
  rules?: object;
  secureTextEntry?: boolean;
  placeholder?: string;
  [key: string]: any;
}

export const AppTextField: React.FC<AppTextFieldProps> = ({
  nameField,
  label,
  defaultValue = "",
  rules = {},
  secureTextEntry = false,
  placeholder = "",
  ...rest
}) => {
  const { control } = useFormContext();

  return (
    <Controller
      control={control}
      name={nameField}
      defaultValue={defaultValue}
      rules={rules}
      render={({ field: { onChange, onBlur, value }, fieldState: { error } }) => (
        <>
          <TextInput
            mode="outlined"
            label={label}
            placeholder={placeholder}
            value={value || defaultValue}
            onBlur={onBlur}
            onChangeText={onChange}
            secureTextEntry={secureTextEntry}
            outlineColor="#49454F"
            activeOutlineColor="#49454F"
            placeholderTextColor="#49454F"
            textColor="#000000"
            theme={{
              colors: {
                onSurfaceVariant: "#49454F",
                placeholder: "#49454F",
                text: "#000000",
              },
            }}
            style={styles.input}
            {...rest}
          />
          {error && <Text style={styles.errorText}>{error.message}</Text>}
        </>
      )}
    />
  );
};

const styles = StyleSheet.create({
  input: {
    width: "100%",
    marginBottom: 15,
    backgroundColor: "#FFFFFF",
    borderRadius: 8,
  },
  errorText: {
    color: "red",
    fontSize: 12,
    marginTop: -10,
    marginBottom: 10,
  },
});