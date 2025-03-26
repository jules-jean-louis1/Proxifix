import AsyncStorage from "@react-native-async-storage/async-storage";
import { jwtDecode } from "jwt-decode";

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


let cachedToken: any | null = null;

export const getDecodedToken = () => {
    if (cachedToken) return Promise.resolve(cachedToken);

    return AsyncStorage.getItem("userToken").then((token) => {
        cachedToken = token ? jwtDecode<any>(token) : null;
        return cachedToken;
    });
};

export const canAccessAdmin = () => {
    return getDecodedToken().then((decoded) => {
        return decoded?.roles?.some((role: string) => 
            role === USER_ROLE.ADMIN || role === USER_ROLE.TECHNICIAN
        ) ?? false;
    });
};