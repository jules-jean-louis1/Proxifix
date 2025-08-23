import * as React from 'react';
import { Controller, useFormContext } from 'react-hook-form';
import { View, Text } from 'react-native';
import { DatePickerInput } from 'react-native-paper-dates';

type AppDateInputFieldProps = {
  nameField: string;
  label: string;
  defaultValue?: Date;
  placeholder?: string;
  rules?: Record<string, any>;
};

export const AppDateInputField: React.FC<AppDateInputFieldProps> = ({
  nameField,
  label,
  defaultValue = new Date(),
  placeholder = 'Sélectionner une date',
  rules = {},
}: AppDateInputFieldProps) => {
  const [inputDate, setInputDate] = React.useState<Date | undefined>(undefined);
  const { control } = useFormContext();

  return (
    <Controller
      control={control}
      name={nameField}
      defaultValue={defaultValue}
      rules={rules}
      render={({ field: { onChange, value }, fieldState: { error } }) => (
        <View style={{ marginVertical: 8 }}>
          <DatePickerInput
            locale="fr"
            label={label}
            value={value}
            onChange={date => {
              onChange(date);
              setInputDate(date);
            }}
            inputMode="start"
            withModal={false}
            mode="outlined"
            placeholder={placeholder}
          />
          {error && <Text style={{ color: 'red' }}>{error.message}</Text>}
        </View>
      )}
    />
  );
};
