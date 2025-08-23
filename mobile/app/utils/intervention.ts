export enum Intervention_Status {
  'PENDING' = 'pending',
  'ASSIGNED' = 'assigned',
  'AWAITING' = 'awaiting_pickup',
  'IN_PROGRESS' = 'in_progress',
  'COMPLETED' = 'completed',
  'CANCELED' = 'canceled',
}

export const INTERVENTION_STATUS = {
  PENDING: Intervention_Status.PENDING,
  ASSIGNED: Intervention_Status.ASSIGNED,
  AWAITING: Intervention_Status.AWAITING,
  IN_PROGRESS: Intervention_Status.IN_PROGRESS,
  COMPLETED: Intervention_Status.COMPLETED,
  CANCELED: Intervention_Status.CANCELED,
} as const;

export const getStatusColor = (status: string) => {
  switch (status) {
    case INTERVENTION_STATUS.PENDING:
      return '#FF9800'; // Amber
    case INTERVENTION_STATUS.ASSIGNED:
      return '#FF5722'; // Deep Orange
    case INTERVENTION_STATUS.AWAITING:
      return '#2196F3'; // Blue
    case INTERVENTION_STATUS.IN_PROGRESS:
      return '#4CAF50'; // Green
    case INTERVENTION_STATUS.COMPLETED:
      return '#8BC34A'; // Light Green
    case INTERVENTION_STATUS.CANCELED:
      return '#F44336'; // Red
    default:
      return '#000000'; // Default color (black)
  }
};

export const getStatusColorBackground = (status: string) => {
  switch (status) {
    case INTERVENTION_STATUS.PENDING:
      return 'rgba(255, 152, 0, 0.2)'; // Amber
    case INTERVENTION_STATUS.ASSIGNED:
      return 'rgba(255, 87, 34, 0.2)'; // Deep Orange
    case INTERVENTION_STATUS.AWAITING:
      return 'rgba(33, 150, 243, 0.2)'; // Blue
    case INTERVENTION_STATUS.IN_PROGRESS:
      return 'rgba(76, 175, 80, 0.2)'; // Green
    case INTERVENTION_STATUS.COMPLETED:
      return 'rgba(139, 195, 74, 0.2)'; // Light Green
    case INTERVENTION_STATUS.CANCELED:
      return 'rgba(244, 67, 54, 0.2)'; // Red
    default:
      return 'rgba(0, 0, 0, 0.2)'; // Default color (black)
  }
};

export const getStatus = (intervention: any): string => {
  switch (intervention.status) {
    case INTERVENTION_STATUS.PENDING:
      return 'En attente';
    case INTERVENTION_STATUS.ASSIGNED:
      return 'Assigné(e)';
    case INTERVENTION_STATUS.AWAITING:
      return 'En attente de récupération';
    case INTERVENTION_STATUS.IN_PROGRESS:
      return 'En traitement';
    case INTERVENTION_STATUS.COMPLETED:
      return 'Complété(e)';
    case INTERVENTION_STATUS.CANCELED:
      return 'Annulé(e)';
    default:
      return '';
  }
};

export enum Appointment_Status {
  'pending' = 'pending',
  'accepted' = 'accepted',
  'rejected' = 'rejected',
}
export const APPOINTMENT_STATUS = {
  PENDING: Appointment_Status.pending,
  ACCEPTED: Appointment_Status.accepted,
  REJECTED: Appointment_Status.rejected,
} as const;

export const getStatusAppointmentCard = (status: string): string => {
  switch (status) {
    case APPOINTMENT_STATUS.PENDING:
      return 'En attente';
    case APPOINTMENT_STATUS.ACCEPTED:
      return 'Accepté';
    case APPOINTMENT_STATUS.REJECTED:
      return 'Rejeté';
    default:
      return 'Statut inconnu';
  }
};
