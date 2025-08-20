import React, { useState, useEffect } from "react";
import { View, Text, TouchableOpacity, FlatList, StyleSheet } from "react-native";
import { Controller, useFormContext } from "react-hook-form";
import { useApi } from "@/app/hooks/useApi";
import { TextInput } from "react-native-paper";

interface AppSimpleSearchInputProps {
  nameField: string;
  label?: string;
  placeholder?: string;
  searchEndpoint: string;
  displayKey: (item: any) => string;
  valueKey: string;
  rules?: object;
}

export const AppSimpleSearchInput: React.FC<AppSimpleSearchInputProps> = ({
  nameField,
  label,
  placeholder,
  searchEndpoint,
  displayKey,
  valueKey,
  rules,
}) => {
  const { control } = useFormContext();
  const api = useApi();
  
  const [searchText, setSearchText] = useState("");
  const [results, setResults] = useState<any[]>([]);
  const [showResults, setShowResults] = useState(false);
  const [selectedItem, setSelectedItem] = useState<any>(null);

  // Fonction de recherche simple
  const searchItems = async (query: string) => {
    if (query.length < 2) {
      setResults([]);
      setShowResults(false);
      return;
    }

    try {
      const response = await api.get(`${searchEndpoint}${encodeURIComponent(query)}`);
      setResults(response.data || []);
      setShowResults(true);
    } catch (error) {
      setResults([]);
    }
  };

  // Debouncing simple
  useEffect(() => {
    const timer = setTimeout(() => {
      if (searchText) {
        searchItems(searchText);
      }
    }, 500);

    return () => clearTimeout(timer);
  }, [searchText]);

  const selectItem = (item: any, onChange: any) => {
    setSelectedItem(item);
    setSearchText(displayKey(item));
    setShowResults(false);
    onChange(item[valueKey]);
  };

  return (
    <View style={styles.container}>
      {label && <Text style={styles.label}>{label}</Text>}
      
      <Controller
        control={control}
        name={nameField}
        rules={rules}
        render={({ field: { onChange, value }, fieldState: { error } }) => (
          <>
            <TextInput
              style={[styles.input, error && styles.inputError]}
              value={searchText}
              onChangeText={(text) => {
                setSearchText(text);
                if (!text) {
                  onChange("");
                  setSelectedItem(null);
                }
              }}
              placeholder={placeholder || "Tapez pour rechercher..."}
              onFocus={() => {
                if (results.length > 0) {
                  setShowResults(true);
                }
              }}
            />

            {showResults && results.length > 0 && (
              <View style={styles.resultsContainer}>
                <FlatList
                  data={results}
                  keyExtractor={(item) => item[valueKey]?.toString()}
                  renderItem={({ item }) => (
                    <TouchableOpacity
                      style={styles.resultItem}
                      onPress={() => selectItem(item, onChange)}
                    >
                      <Text style={styles.resultText}>{displayKey(item)}</Text>
                    </TouchableOpacity>
                  )}
                  style={styles.resultsList}
                  nestedScrollEnabled
                />
              </View>
            )}

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
    zIndex: 1000,
  },
  label: {
    fontSize: 14,
    marginBottom: 4,
    color: "#364A63",
    fontWeight: "bold",
  },
  input: {
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: "#fff",
  },
  inputError: {
    borderColor: "#B00020",
  },
  resultsContainer: {
    position: "relative",
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderTopWidth: 0,
    borderBottomLeftRadius: 8,
    borderBottomRightRadius: 8,
    maxHeight: 200,
    zIndex: 1001,
  },
  resultsList: {
    maxHeight: 200,
  },
  resultItem: {
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#F0F0F0",
  },
  resultText: {
    fontSize: 16,
    color: "#333",
  },
  errorText: {
    marginTop: 4,
    fontSize: 12,
    color: "#B00020",
  },
});
