import { SessionToken } from "../utils/types";

export enum USER_ROLE {
  "SUPER_ADMIN" = "ROLE_SUPER_ADMIN",
  "ADMIN" = "ROLE_ADMIN",
  "TECHNICIAN" = "ROLE_TECHNICIAN",
  "CUSTOMER" = "ROLE_CUSTOMER",
}

export const USER_AVAILABLES_ROLES = [
  USER_ROLE.SUPER_ADMIN,
  USER_ROLE.ADMIN,
  USER_ROLE.TECHNICIAN,
  USER_ROLE.CUSTOMER,
] as const;

export const getIsAdmin = (sessionData: SessionToken) => {
  if (
    sessionData?.role === USER_ROLE.ADMIN ||
    sessionData?.role === USER_ROLE.TECHNICIAN ||
    sessionData?.role === USER_ROLE.SUPER_ADMIN
  ) {
    return true;
  }
  return false;
};

export const isTechnician = (sessionData: SessionToken) => {
  if (sessionData?.role === USER_ROLE.TECHNICIAN) {
    return true;
  }
  return false;
};

export const isCustomer = (sessionData: SessionToken) => {
  if (sessionData?.role === USER_ROLE.CUSTOMER) {
    return true;
  }
  return false;
};
export const isSuperAdmin = (sessionData: SessionToken) => {
  if (sessionData?.role === USER_ROLE.SUPER_ADMIN) {
    return true;
  }
  return false;
};
export const isAdmin = (sessionData: SessionToken) => {
  if (sessionData?.role === USER_ROLE.ADMIN) {
    return true;
  }
  return false;
};