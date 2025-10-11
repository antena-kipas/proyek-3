import React from 'react';
import { Modal, View, Text, StyleSheet, TouchableOpacity, ActivityIndicator } from 'react-native';

interface ConfirmationModalProps {
  visible: boolean;
  status: 'confirm' | 'loading' | 'success' | 'error';
  onConfirm: () => void;
  onCancel: () => void;
  onRetry?: () => void;
  title: string;
  message: string;
  messageColor?: string;
  successMessage?: string;
  errorMessage?: string;
  documentType?: string;
  timestamp?: string;
}

const ConfirmationModal: React.FC<ConfirmationModalProps> = ({
  visible,
  status,
  onConfirm,
  onCancel,
  onRetry,
  title,
  message,
  messageColor = 'black',
  successMessage = 'BERHASIL Dibuat',
  errorMessage = 'GAGAL Dibuat',
  documentType = 'Dokumen',
  timestamp,
}) => {
  const renderContent = () => {
    switch (status) {
      case 'loading':
        return <ActivityIndicator size="large" color="#0000ff" />;
      case 'success':
        return (
          <>
            <View style={styles.successHeader}>
              <Text style={styles.successHeaderText}>Pemberitahuan</Text>
            </View>
            <View style={styles.successBody}>
              <Text style={styles.successBodyText}>{documentType}:</Text>
              <Text style={styles.successBodyText}>{successMessage}</Text>
              {timestamp && <Text style={styles.successTimestampText}>{timestamp}</Text>}
            </View>
            <TouchableOpacity style={styles.successButton} onPress={onCancel}>
              <Text style={styles.modalButtonText}>OK</Text>
            </TouchableOpacity>
          </>
        );
      case 'error':
        return (
          <>
            <View style={styles.errorHeader}>
              <Text style={styles.errorHeaderText}>Pemberitahuan</Text>
            </View>
            <View style={styles.errorBody}>
              <Text style={styles.errorBodyText}>{documentType}:</Text>
              <Text style={styles.errorBodyText}>{errorMessage}</Text>
            </View>
            <View style={styles.modalButtonContainer}>
              <TouchableOpacity
                style={[styles.modalButton, styles.errorCloseButton]}
                onPress={onCancel}
              >
                <Text style={styles.modalButtonText}>Tutup</Text>
              </TouchableOpacity>
              {onRetry && (
                <TouchableOpacity
                  style={[styles.modalButton, styles.errorRetryButton]}
                  onPress={onRetry}
                >
                  <Text style={styles.modalButtonText}>Coba Lagi</Text>
                </TouchableOpacity>
              )}
            </View>
          </>
        );
      case 'confirm':
      default:
        return (
          <>
            <Text style={styles.modalTitle}>{title}</Text>
            <Text style={[styles.modalText, { color: messageColor }]}>{message}</Text>
            <View style={styles.modalButtonContainer}>
              <TouchableOpacity
                style={[styles.modalButton, styles.modalButtonNo]}
                onPress={onCancel}
              >
                <Text style={styles.modalButtonText}>TIDAK</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.modalButton, styles.modalButtonYes]}
                onPress={onConfirm}
              >
                <Text style={styles.modalButtonText}>YA</Text>
              </TouchableOpacity>
            </View>
          </>
        );
    }
  };

  return (
    <Modal
      animationType="fade"
      transparent={true}
      visible={visible}
      onRequestClose={onCancel}
    >
      <View style={styles.centeredView}>
        <View style={[styles.modalView, (status === 'success' || status === 'error') && styles.statusModalView]}>
          {renderContent()}
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  centeredView: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  modalView: {
    margin: 20,
    backgroundColor: 'white',
    borderRadius: 14,
    padding: 35,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 4,
    elevation: 5,
    width: '80%',
    minHeight: 180,
    justifyContent: 'center',
  },
  statusModalView: {
    padding: 0,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 15,
    textAlign: 'center',
    color: 'black',
  },
  modalText: {
    marginBottom: 25,
    textAlign: 'center',
    color: 'black',
  },
  modalButtonContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    width: '100%',
    paddingHorizontal: 20,
    paddingBottom: 20,
  },
  modalButton: {
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 30,
    elevation: 2,
    marginHorizontal: 10,
  },
  modalButtonNo: {
    backgroundColor: '#FF3B30',
  },
  modalButtonYes: {
    backgroundColor: '#4CD964',
  },
  modalButtonText: {
    color: 'white',
    fontWeight: 'bold',
    textAlign: 'center',
  },
  // Success Styles
  successHeader: {
    backgroundColor: 'blue',
    width: '100%',
    padding: 15,
    borderTopLeftRadius: 14,
    borderTopRightRadius: 14,
    alignItems: 'center',
  },
  successHeaderText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  successBody: {
    padding: 30,
    alignItems: 'flex-start',
    width: '100%',
  },
  successBodyText: {
    color: 'black',
    fontSize: 16,
    marginBottom: 5,
  },
  successTimestampText: {
    color: 'gray',
    fontSize: 14,
    marginTop: 10,
  },
  successButton: {
    backgroundColor: '#007AFF',
    borderRadius: 10,
    paddingVertical: 12,
    paddingHorizontal: 40,
    elevation: 2,
    marginBottom: 20,
  },
  // Error Styles
  errorHeader: {
    backgroundColor: '#D32F2F', // Red color for error
    width: '100%',
    padding: 15,
    borderTopLeftRadius: 14,
    borderTopRightRadius: 14,
    alignItems: 'center',
  },
  errorHeaderText: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  errorBody: {
    padding: 30,
    alignItems: 'flex-start',
    width: '100%',
  },
  errorBodyText: {
    color: 'black',
    fontSize: 16,
    marginBottom: 5,
  },
  errorCloseButton: {
    backgroundColor: '#BDBDBD', // Gray color for close
  },
  errorRetryButton: {
    backgroundColor: '#007AFF', // Blue color for retry
  },
});

export default ConfirmationModal;