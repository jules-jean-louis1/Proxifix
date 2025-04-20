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
    sessionData?.roles.some(
      (role: string) =>
        role === USER_ROLE.ADMIN || role === USER_ROLE.TECHNICIAN
    )
  ) {
    return true;
  }
  return false;
};
