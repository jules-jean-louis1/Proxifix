import { Feather } from '@expo/vector-icons';
import { View, StyleSheet, Pressable, Text } from 'react-native';
import React from 'react';

interface ToolBarAdminProps {
  title?: string;
  showBack?: boolean;
  onBackPress?: () => void;
  rightContent?: React.ReactNode;
  bottomBar?: boolean;
}

export const ToolBarAdmin: React.FC<ToolBarAdminProps> = ({
  title = '',
  showBack = false,
  onBackPress,
  rightContent,
  bottomBar = false,
}) => {
  return (
    <View style={[bottomBar ? styles.container : styles.containerBarLess]}>
      <View style={styles.left}>
        {showBack ? (
          <Pressable onPress={onBackPress} style={styles.iconButton}>
            <Feather name="arrow-left" size={24} color="#344260" />
          </Pressable>
        ) : (
          <View style={styles.iconButton} />
        )}
      </View>
      <View style={{ flex: 1, alignItems: 'center' }}>
        {title ? <Text style={styles.title}>{title}</Text> : null}
      </View>
      <View style={styles.right}>{rightContent}</View>
    </View>
  );
};

const styles = StyleSheet.create({
  containerHome: {
    width: '100%',
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
  },
  container: {
    width: '100%',
    backgroundColor: '#fff',
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 16,
    justifyContent: 'space-between',
    borderBottomWidth: 1,
    borderBottomColor: '#283276',
  },
  containerBarLess: {
    width: '100%',
    backgroundColor: '#fff',
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 16,
    justifyContent: 'space-between',
  },
  left: {
    width: 40,
    alignItems: 'flex-start',
  },
  right: {
    width: 40,
    alignItems: 'flex-end',
  },
  iconButton: {
    padding: 6,
    borderRadius: 20,
    height: 36,
    width: 36,
    justifyContent: 'center',
    alignItems: 'center',
  },
  title: {
    flex: 1,
    textAlign: 'center',
    fontSize: 16,
    fontWeight: 'light',
    color: '#344260',
  },
  subContainer: {
    paddingVertical: 5,
    flexDirection: 'row',
    justifyContent: 'flex-end',
    width: '90%',
  },
});
