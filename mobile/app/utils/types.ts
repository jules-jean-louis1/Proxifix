export interface SessionToken {
  iat: number;
  exp: number;
  roles: string[];
  username: string;
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  company: {
    id: number | null;
    name: string | null;
  };
  token: string | null;
}

export interface AuthTokens {
  token: string;
  refreshToken: string;
}
