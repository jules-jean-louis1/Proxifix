import React from 'react';
import { Button, ButtonProps } from 'react-native-paper';
import { StyleSheet, TextStyle, ViewStyle } from 'react-native';

interface AppButtonProps extends ButtonProps {
  type: 'primary' | 'secondary' | 'tertiary';
}

export const AppButton: React.FC<AppButtonProps> = ({
  children,
  type,
  style,
  ...props
}) => {
  const buttonStyle: ViewStyle = styles[type];
  const textStyle: TextStyle = styles[`${type}Text`];

  return (
    <Button
      mode="contained"
      style={[buttonStyle, style]}
      labelStyle={textStyle}
      contentStyle={styles.buttonContent}
      {...props}
    >
      {children}
    </Button>
  );
};

const styles = StyleSheet.create({
  buttonContent: {
    paddingVertical: 4,
    paddingHorizontal: 16,
    minHeight: 48,
  },
  primary: {
    backgroundColor: '#F9556D',
    width: '100%',
    marginVertical: 10,
    borderRadius: 8,
  },
  primaryText: {
    color: '#FFFFFF',
    fontFamily: 'Rubik-Bold.ttf',
    fontSize: 16,
  },
  secondary: {
    backgroundColor: '#F0F3F4',
    width: '100%',
    marginVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#F9556D',
  },
  secondaryText: {
    color: '#F9556D',
    fontFamily: 'Rubik-Bold.ttf',
    fontSize: 16,
  },
  tertiary: {
    backgroundColor: '#01358D',
    width: '100%',
    marginVertical: 10,
    borderRadius: 8,
  },
  tertiaryText: {
    color: '#FFFFFF',
    fontFamily: 'Rubik-Bold.ttf',
    fontSize: 16,
  },
});
