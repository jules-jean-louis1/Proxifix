import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Controller, useFormContext } from 'react-hook-form';
import DropdownSelect from 'react-native-input-select';

interface AppSelectInputProps {
  nameField: string;
  label?: string;
  placeholder?: string;
  options: { label: string; value: any }[];
  rules?: object;
  defaultValue?: any;
}

export const AppSelectInput: React.FC<AppSelectInputProps> = ({
  nameField,
  label,
  placeholder,
  options,
  rules,
  defaultValue = '',
}) => {
  const { control } = useFormContext();

  return (
    <View style={styles.container}>
      <Controller
        control={control}
        name={nameField}
        defaultValue={defaultValue}
        rules={rules}
        render={({ field: { onChange, value }, fieldState: { error } }) => (
          <>
            <DropdownSelect
              label={label}
              placeholder={placeholder || 'Sélectionnez une option'}
              options={options}
              selectedValue={value || defaultValue}
              onValueChange={onChange}
              isSearchable
              primaryColor="purple"
            />
            {error && <Text style={styles.errorText}>{error.message}</Text>}
          </>
        )}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: { marginBottom: 16 },
  label: {
    fontSize: 14,
    marginBottom: 4,
    color: '#364A63',
    fontWeight: 'bold',
  },
  errorText: { marginTop: 4, fontSize: 12, color: '#B00020' },
});
