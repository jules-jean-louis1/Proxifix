import React from 'react';
import { render, fireEvent } from '@testing-library/react-native';
import { AdminCustomerCard } from '../../app/components/admin/customer/AdminCustomerCard';

const mockCustomer = {
  id: 1,
  first_name: 'John',
  last_name: 'Doe',
  email: 'john.doe@test.com',
  phone: '+33123456789',
  role: 'ROLE_CUSTOMER',
  created_at: '2025-01-01T00:00:00Z',
  updated_at: '2025-01-01T00:00:00Z',
  address: '123 Test Street',
  city: 'Test City',
  postal_code: '12345',
  zipcode: '12345',
  equipment: [],
  appointmentRequests: [],
  company: {
    id: 1,
    name: 'Test Company'
  }
};

describe('AdminCustomerCard Component', () => {
  it('renders customer information correctly', () => {
    const { getByText } = render(
      <AdminCustomerCard customer={mockCustomer} onPress={() => {}} />
    );

    // Verify customer information is displayed
    expect(getByText('John Doe')).toBeTruthy();
    expect(getByText('john.doe@test.com')).toBeTruthy();
  });

  it('calls onPress when card is pressed', () => {
    const mockOnPress = jest.fn();
    
    const { getByText } = render(
      <AdminCustomerCard 
        customer={mockCustomer} 
        onPress={mockOnPress}
      />
    );

    // Find the card by customer name and simulate a press
    const card = getByText('John Doe');
    fireEvent.press(card);
    
    expect(mockOnPress).toHaveBeenCalledTimes(1);
  });

  it('displays customer stats correctly', () => {
    const { getByText } = render(
      <AdminCustomerCard customer={mockCustomer} onPress={() => {}} />
    );

    // Verify customer stats are displayed
    expect(getByText('0 équipement')).toBeTruthy();
    expect(getByText('0 demande')).toBeTruthy();
  });

  it('handles customer without company gracefully', () => {
    const customerWithoutCompany = {
      ...mockCustomer,
      company: null
    };
    
    const { queryByText } = render(
      <AdminCustomerCard customer={customerWithoutCompany} onPress={() => {}} />
    );

    // Verify the app does not crash without company
    expect(queryByText('John Doe')).toBeTruthy();
  });
});
