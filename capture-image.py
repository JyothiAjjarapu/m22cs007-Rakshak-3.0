import fitz  # PyMuPDF
import numpy as np
from PIL import Image
import os
import sys
import re
from datetime import datetime

# Check if the script is run from the command line with the correct argument
if len(sys.argv) != 2:
    print("Usage: python capture-image.py <pdf_filename>")
    sys.exit(1)

pdf_filename = sys.argv[1]

# Extract patient ID from the PDF filename using regular expression
match = re.match(r'^([a-zA-Z_]+)_\d+_.+\.pdf$', pdf_filename)
if not match:
    print(f"Error: Unable to extract patient ID from PDF filename '{pdf_filename}'")
    sys.exit(1)

patient_id = match.group(1)

# Create the full path to the uploaded file in XAMPP's environment
uploads_folder = 'uploads/'
pdf_path = os.path.join(uploads_folder, pdf_filename)

# Check if the file exists
if not os.path.exists(pdf_path):
    print(f"Error: The specified PDF file '{pdf_filename}' does not exist in the 'uploads' folder.")
    sys.exit(1)

# Open PDF using PyMuPDF
pdf_document = fitz.open(pdf_path)

# Assume you want to capture the second page (index 1)
page = pdf_document[1]
image = page.get_pixmap()

# Convert the image data to a NumPy array
image_array = np.array(Image.frombytes("RGB", (image.width, image.height), image.samples))

# Construct the new image filename with patient ID and timestamp
timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
image_filename = f"{patient_id}_{timestamp}.png"
images_folder = 'captured/'
image_path = os.path.join(images_folder, image_filename)

# Save the image
Image.fromarray(image_array).save(image_path)

print(f"Image saved successfully at: {image_path}")
