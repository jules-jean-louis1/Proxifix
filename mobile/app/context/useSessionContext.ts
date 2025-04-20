import { jwtDecode } from "jwt-decode";
import { useSession } from "./ctx";
import { getIsAdmin } from "./user";
import { SessionToken } from "../utils/types";

export const useSessionContext = () => {
  const { session } = useSession();
  if (!session) return null;

  const parsedSession = JSON.parse(session);

  const sessionData = jwtDecode<SessionToken>(parsedSession.token);

  return {
    session: sessionData,
    getIsAdmin: () => getIsAdmin(sessionData),
    getSessionToken: () => parsedSession.token,
    getSessionRefreshToken: () => parsedSession.refreshToken,
  };
};
