export enum Intervention_Status {
  "PENDING" = "pending",
  "AWAITING" = "awaiting_pickup",
  "IN_PROGRESS" = "in_progress",
  "COMPLETED" = "completed",
  "CANCELED" = "canceled",
}

export const INTERVENTION_STATUS = {
  PENDING: Intervention_Status.PENDING,
  AWAITING: Intervention_Status.AWAITING,
  IN_PROGRESS: Intervention_Status.IN_PROGRESS,
  COMPLETED: Intervention_Status.COMPLETED,
  CANCELED: Intervention_Status.CANCELED,
} as const;

export const getStatusColor = (status: string) => {
  switch (status) {
    case INTERVENTION_STATUS.PENDING:
      return "#FF9800"; // Amber
    case INTERVENTION_STATUS.AWAITING:
      return "#2196F3"; // Blue
    case INTERVENTION_STATUS.IN_PROGRESS:
      return "#4CAF50"; // Green
    case INTERVENTION_STATUS.COMPLETED:
      return "#8BC34A"; // Light Green
    case INTERVENTION_STATUS.CANCELED:
      return "#F44336"; // Red
    default:
      return "#000000"; // Default color (black)
  }
};

export const getStatus = (intervention: any): string => {
  switch (intervention.status) {
    case INTERVENTION_STATUS.PENDING:
      return "En attente";
    case INTERVENTION_STATUS.AWAITING:
      return "En attente de récupération";
    case INTERVENTION_STATUS.IN_PROGRESS:
      return "En traitement";
    case INTERVENTION_STATUS.COMPLETED:
      return "Complété(e)";
    case INTERVENTION_STATUS.CANCELED:
      return "Annulé(e)";
    default:
      return "";
  }
};

export enum Appointment_Status {
  "pending" = "pending",
  "accepted" = "accepted",
  "rejected" = "rejected",
}
export const APPOINTMENT_STATUS = {
  PENDING: Appointment_Status.pending,
  ACCEPTED: Appointment_Status.accepted,
  REJECTED: Appointment_Status.rejected,
} as const;

export const getStatusAppointmentCard = (status: string):string => {
  switch (status) {
    case APPOINTMENT_STATUS.PENDING:
      return "En attente";
    case APPOINTMENT_STATUS.ACCEPTED:
      return "Accepté";
    case APPOINTMENT_STATUS.REJECTED:
      return "Rejeté";
    default:
      return "Statut inconnu";
  }
}