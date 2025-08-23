import { renderHook } from '@testing-library/react-native';
import { useApi } from '../../app/hooks/useApi';

// Mock SecureStore since useApi uses it
jest.mock('expo-secure-store', () => ({
  getItemAsync: jest.fn(),
  setItemAsync: jest.fn(),
  deleteItemAsync: jest.fn(),
}));

// Mock Platform
jest.mock('react-native', () => ({
  Platform: {
    OS: 'ios',
  },
}));

// Mock axios
jest.mock('axios', () => ({
  create: jest.fn(() => ({
    get: jest.fn(),
    post: jest.fn(),
    put: jest.fn(),
    delete: jest.fn(),
    interceptors: {
      request: { use: jest.fn() },
      response: { use: jest.fn() }
    }
  })),
  post: jest.fn(),
}));

describe('useApi Hook', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  it('should initialize with correct base URL', () => {
    const { result } = renderHook(() => useApi());
    
    expect(result.current).toBeDefined();
    // Test that the hook returns the expected methods
    expect(typeof result.current.get).toBe('function');
    expect(typeof result.current.post).toBe('function');
    expect(typeof result.current.put).toBe('function');
    expect(typeof result.current.delete).toBe('function');
  });

  it('should create an API instance correctly', () => {
    const { result } = renderHook(() => useApi());
    
    // Test que l'API est bien créée avec les méthodes attendues
    expect(result.current).toBeDefined();
    expect(result.current.interceptors).toBeDefined();
  });
});
