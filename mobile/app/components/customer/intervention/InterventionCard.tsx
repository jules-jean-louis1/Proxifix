import React from 'react';
import { Pressable, Text, View, StyleSheet } from 'react-native';
import { Feather } from '@expo/vector-icons';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import {
  getStatus,
  getStatusColor,
  getStatusColorBackground,
} from '@/app/utils/intervention';

interface InterventionCardProps {
  intervention?: any;
  onPress?: () => void;
}

export const InterventionCard: React.FC<InterventionCardProps> = ({
  intervention,
  onPress,
}) => {
  return (
    <Pressable onPress={onPress}>
      <View style={styles.interventionItem}>
        <View style={styles.titleFlex}>
          <Text style={styles.interventionTitle}>{intervention.title}</Text>
        </View>

        <View style={styles.containerInformation}>
          <Feather
            name="briefcase"
            size={25}
            color="#000"
            style={styles.iconLeft}
          />
          <Text style={styles.interventionDescription}>
            {intervention.company && intervention.company.name
              ? intervention.company.name
              : 'Aucune entreprise associée'}
          </Text>
        </View>

        <View style={styles.containerInformation}>
          <Feather
            name="package"
            size={25}
            color="#000"
            style={styles.iconLeft}
          ></Feather>
          {intervention.equipment ? (
            <Text style={styles.interventionDescription}>
              {intervention.equipment.name}
            </Text>
          ) : (
            <Text style={styles.interventionDescription}>
              Aucun équipement associé
            </Text>
          )}
        </View>

        <View style={styles.footerContainer}>
          <View style={styles.dateContainer}>
            {intervention.created_at && (
              <Text style={styles.footerDate}>
                {format(new Date(intervention.created_at), 'dd MMMM yyyy', {
                  locale: fr,
                })}
              </Text>
            )}
            <Feather
              name="calendar"
              size={17}
              color="#4BC0C0"
              style={styles.icon}
            />
          </View>
          <Text
            style={{
              fontSize: 12,
              fontWeight: '600',
              color: getStatusColor(intervention.status),
              backgroundColor: getStatusColorBackground(intervention.status),
              padding: 8,
              borderRadius: 8,
            }}
          >
            {getStatus(intervention)}
          </Text>
        </View>
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  interventionItem: {
    padding: 16,
    backgroundColor: '#fff',
    borderRadius: 8,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  interventionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#E53953',
    marginBottom: 5,
  },
  interventionDescription: {
    fontSize: 16,
    color: '#5B6880',
    marginBottom: 5,
  },
  containerInformation: {
    flex: 1,
    flexDirection: 'row',
    marginBottom: 5,
  },
  footerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 10,
  },
  statusContainer: {
    padding: 8,
    borderRadius: 8,
  },
  dateContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(75, 192, 192, 0.2)',
    padding: 8,
    borderRadius: 8,
    marginRight: 8,
  },
  dateText: {
    fontSize: 13,
    color: '#4BC0C0',
    fontWeight: 'bold',
  },
  icon: {
    paddingLeft: 5,
  },
  iconLeft: {
    marginRight: 10,
  },
  status: {
    fontSize: 13,
    fontWeight: 'bold',
    backgroundColor: 'rgba(0, 119, 193, 0.2)',
    color: '#48A3D7',
    padding: 8,
    borderRadius: 8,
    marginRight: 8,
    textAlign: 'center',
  },
  footerDate: {
    fontSize: 14,
    color: '#5B6880',
    textAlign: 'right',
  },
  titleFlex: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
  },
});
