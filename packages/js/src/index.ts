/**
 * Authenticator JS SDK
 * @ataurbdx/authenticator
 */

export interface AuthenticatorConfig {
  baseUrl: string;
  csrfToken?: string;
  onSuccess?: (data: any) => void;
  onError?: (error: any) => void;
}

export class AuthenticatorClient {
  private config: AuthenticatorConfig;

  constructor(config: AuthenticatorConfig) {
    this.config = config;
  }

  // SDK methods will be implemented in future development
}
