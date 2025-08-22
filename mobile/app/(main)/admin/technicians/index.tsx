import { useRouter, useFocusEffect } from 'expo-router';
import { useApi } from '@/app/hooks/useApi';
import { FC, useEffect, useState, useCallback } from 'react';
import { useSessionContext } from '@/app/context/useSessionContext';
import { ScrollView, StyleSheet, View } from 'react-native';
import React from 'react';
import { AdminTechnicianCard } from '@/app/components/admin/technician/AdminTechnicianCard';
import { FAB } from 'react-native-paper';
import { AppTextField } from '@/app/components/inputs/AppTextField';
import { FormProvider, useForm } from 'react-hook-form';
import { ToolBarAdmin } from '@/app/components/admin/navigation/ToolBarAdmin';

const AdminTechniciansPage: FC = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [technicians, setTechnicians] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [searchQuery, setSearchQuery] = useState<string>('');

  const methods = useForm({
    defaultValues: {
      search: '',
    },
  });

  const loadTechnicians = useCallback(
    async (search: string = '') => {
      if (!sessionData?.company?.id) return;

      setIsLoading(true);
      try {
        const url = search
          ? `/user?search=${encodeURIComponent(search)}&role=ROLE_TECHNICIAN`
          : `/user?role=ROLE_TECHNICIAN`;
        const response = await api.get(url);
        setTechnicians(response.data);
      } catch (error) {
        console.error('Erreur chargement techniciens:', error);
      } finally {
        setIsLoading(false);
      }
    },
    [sessionData?.company?.id, api]
  );

  const searchValue = methods.watch('search');

  // Recharger la liste quand on revient sur cette page
  useFocusEffect(
    useCallback(() => {
      loadTechnicians(searchValue);
    }, [loadTechnicians, searchValue])
  );

  // Surveillance du champ de recherche avec debounce
  useEffect(() => {
    if (searchValue === '') {
      return; // useFocusEffect s'occupe du chargement initial
    }

    const timeoutId = setTimeout(() => {
      loadTechnicians(searchValue);
    }, 500);

    return () => clearTimeout(timeoutId);
  }, [searchValue, loadTechnicians]);

  return (
    <View style={styles.container}>
      <ToolBarAdmin title="Techniciens" bottomBar />

      {/* Barre de recherche */}
      <View style={styles.searchContainer}>
        <FormProvider {...methods}>
          <AppTextField
            nameField="search"
            label="Rechercher un technicien..."
            placeholder="Nom, prénom ou email"
          />
        </FormProvider>
      </View>

      <ScrollView style={styles.scrollView}>
        {isLoading ? (
          <View style={styles.loadingContainer}>
            {/* Ajoutez votre composant de loading ici */}
          </View>
        ) : (
          technicians.map(technician => (
            <AdminTechnicianCard
              key={technician.id}
              technician={technician}
              onPress={() =>
                router.push(`/(main)/admin/technicians/${technician.id}` as any)
              }
            />
          ))
        )}
      </ScrollView>

      <FAB
        icon="plus"
        style={styles.fab}
        label="Ajouter un technicien"
        onPress={() => router.push('/(main)/admin/technicians/new')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  searchContainer: {
    padding: 16,
    backgroundColor: '#f5f5f5',
  },
  scrollView: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  fab: {
    margin: 16,
    marginBottom: 70,
    backgroundColor: '#F9556D',
  },
});

export default AdminTechniciansPage;
