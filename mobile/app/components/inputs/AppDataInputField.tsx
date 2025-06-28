import * as React from 'react';
import { View } from 'react-native';
import { DatePickerInput } from 'react-native-paper-dates';
import { SafeAreaProvider } from 'react-native-safe-area-context';

export default function AppDateInputField() {
  const [inputDate, setInputDate] = React.useState<Date | undefined>(undefined);

  return (
    <SafeAreaProvider>
      <View style={{ padding: 16 }}>
        <DatePickerInput
          locale="fr"
          label="Date"
          value={inputDate}
          onChange={(d) => setInputDate(d)}
          inputMode="start"    
          withModal={false}         
          mode="outlined"
          placeholder="JJ/MM/AAAA"
        />
      </View>
    </SafeAreaProvider>
  );
}
