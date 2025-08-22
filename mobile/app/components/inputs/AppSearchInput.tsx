import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Controller, useFormContext } from 'react-hook-form';
import DropdownSelect from 'react-native-input-select';
import { useApi } from '@/app/hooks/useApi';

interface AppSearchInputProps {
  nameField: string;
  label?: string;
  placeholder?: string;
  searchEndpoint: string; // ex: "/customer"
  searchParams?: object; // ex: { role: "ROLE_CUSTOMER" }
  searchParamName?: string; // nom du paramètre de recherche, défaut: "query"
  displayKey: string | ((item: any) => string); // "full_name" ou fonction pour formater l'affichage
  valueKey: string; // "id" - clé pour la valeur
  minSearchLength?: number; // minimum de caractères pour déclencher la recherche
  debounceMs?: number; // délai en ms pour le debouncing
  rules?: object;
  defaultValue?: any;
  initialOptions?: { label: string; value: any }[]; // options initiales optionnelles
}

export const AppSearchInput: React.FC<AppSearchInputProps> = ({
  nameField,
  label,
  placeholder,
  searchEndpoint,
  searchParams = {},
  searchParamName = 'query',
  displayKey,
  valueKey,
  minSearchLength = 2,
  debounceMs = 300,
  rules,
  defaultValue = '',
  initialOptions = [],
}) => {
  const { control } = useFormContext();
  const api = useApi();

  const [options, setOptions] =
    useState<{ label: string; value: any }[]>(initialOptions);
  const [loading, setLoading] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  // Fonction pour formater l'affichage d'un item
  const formatDisplay = useCallback(
    (item: any): string => {
      if (typeof displayKey === 'function') {
        return displayKey(item);
      }
      return item[displayKey] || '';
    },
    [displayKey]
  );

  // Fonction de recherche avec debouncing
  const searchItems = useCallback(
    async (query: string) => {
      if (query.length < minSearchLength) {
        setOptions(initialOptions);
        return;
      }

      setLoading(true);
      try {
        // Construire les paramètres de recherche
        const params = new URLSearchParams({
          [searchParamName]: query, // Utiliser le nom de paramètre personnalisé
          ...searchParams,
        }).toString();

        console.log(`Recherche: ${searchEndpoint}?${params}`); // Debug
        const response = await api.get(`${searchEndpoint}?${params}`);
        const results = response.data;

        // Transformer les résultats en options pour DropdownSelect
        const formattedOptions = results.map((item: any) => ({
          label: formatDisplay(item),
          value: item[valueKey],
        }));

        setOptions(formattedOptions);
      } catch (error) {
        console.error('Erreur lors de la recherche:', error);
        setOptions([]);
      } finally {
        setLoading(false);
      }
    },
    [
      searchEndpoint,
      searchParams,
      searchParamName,
      minSearchLength,
      formatDisplay,
      valueKey,
      api,
      initialOptions,
    ]
  );

  // Effect pour le debouncing
  useEffect(() => {
    const timer = setTimeout(() => {
      if (searchQuery !== '') {
        searchItems(searchQuery);
      }
    }, debounceMs);

    return () => clearTimeout(timer);
  }, [searchQuery, searchItems, debounceMs]);

  // Handler pour la recherche
  const handleSearch = useCallback((query: string) => {
    console.log('Recherche déclenchée:', query); // Debug
    setSearchQuery(query);
  }, []);

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
              placeholder={placeholder || 'Rechercher et sélectionner...'}
              options={options}
              selectedValue={value || defaultValue}
              onValueChange={onChange}
              isSearchable
              primaryColor="#007AFF"
              searchControls={{
                textInputProps: {
                  placeholder: `Tapez au moins ${minSearchLength} caractères...`,
                  onChangeText: handleSearch,
                },
              }}
              dropdownStyle={{
                borderColor: error ? '#B00020' : '#E0E0E0',
              }}
              placeholderStyle={{
                color: '#7A7A7A',
              }}
              disabled={loading}
            />
            {error && <Text style={styles.errorText}>{error.message}</Text>}
            {loading && (
              <Text style={styles.loadingText}>Recherche en cours...</Text>
            )}
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
  errorText: {
    marginTop: 4,
    fontSize: 12,
    color: '#B00020',
  },
  loadingText: {
    marginTop: 4,
    fontSize: 12,
    color: '#465270',
    fontStyle: 'italic',
  },
});
