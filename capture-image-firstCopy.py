
import fitz  # PyMuPDF
import numpy as np
from PIL import Image
import matplotlib.pyplot as plt
import os
import sys

# Check if the script is run from the command line with the correct argument
if len(sys.argv) != 2:
    print("Usage: python capture-image.py <pdf_filename>")
    sys.exit(1)

pdf_filename = sys.argv[1]

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

# Display or save the image as needed
#plt.imshow(image_array)
#plt.show()

# Store the image in the 'images' folder
images_folder = 'captured/'
image_filename = os.path.splitext(pdf_filename)[0] + '_page2.png'
image_path = os.path.join(images_folder, image_filename)

Image.fromarray(image_array).save(image_path)

print(f"Image saved successfully at: {image_path}")
