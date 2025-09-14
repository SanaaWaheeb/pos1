import { BarcodeDetector } from "barcode-detector";

// Utility function for logging
function log(message) {
    console.log(message);
  }
  
  // Whitelisted barcode formats
  const WHITELISTED_FORMATS = [
    'aztec',
    'code_128',
    'code_39',
    'code_93',
    'codabar',
    'data_matrix',
    'ean_13',
    'ean_8',
    'itf',
    'pdf417',
    'qr_code',
    'upc_a',
    'upc_e'
  ];
  
  /**
   * scanReader class to detect barcodes from images or videos.
   */
  class scanReader {
    static async polyfill() {
      if (!('BarcodeDetector' in window)) {
        try {
          await import('barcode-detector');
          log('Using BarcodeDetector polyfill.');
        } catch (error) {
          throw new Error('BarcodeDetector API is not supported by your browser.', { cause: error });
        }
      } else {
        log('Using the native BarcodeDetector API.');
      }
    }
  
    static async getSupportedFormats() {
      const nativeSupportedFormats = (await window.BarcodeDetector.getSupportedFormats()) || [];
      return WHITELISTED_FORMATS.filter(format => nativeSupportedFormats.includes(format));
    }
  
    static async create(supportedFormats) {
      const isValidFormats = Array.isArray(supportedFormats) && supportedFormats.length > 0;
      const formats = isValidFormats ? supportedFormats : await scanReader.getSupportedFormats();
      return new scanReader(formats);
    }
  
    static async setup() {
      try {
        await scanReader.polyfill();
        return { scanReaderError: null };
      } catch (error) {
        return {
          scanReaderError: error
        };
      }
    }
  
    constructor(formats) {
      this.scanReader = new window.BarcodeDetector({ formats });
    }
  
    async detect(source) {
      if (!this.scanReader) {
        throw new Error('scanReader is not initialized.');
      }
  
      const results = await this.scanReader.detect(source);
  
      if (Array.isArray(results) && results.length > 0) {
        const firstResult = results[0];
  
        log({
          rawValue: firstResult.rawValue,
          format: firstResult.format
        });
  
        return firstResult;
      } else {
        throw new Error('Could not detect barcode from provided source.');
      }
    }
  }
  
  // Export scanReader
  window.scanReader = scanReader;