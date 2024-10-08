import { format } from "date-fns";

export const formatPrice = (price) => {
  // Function to format price with billions and millions

  const billion = Math.floor(price / 1000000000); // Extract billion part
  const million = Math.floor((price % 1000000000) / 1000000); // Extract million part

  let formattedPrice = "";

  if (billion > 0) {
    formattedPrice += `${billion} tỷ `;
    // Only add million part if it's not zero
    if (million > 0) {
      formattedPrice += `${million} triệu`;
    }
  } else if (million > 0) {
    formattedPrice += `${million} triệu`;
  } else {
    // If the price is less than 1 million, format the price normally
    formattedPrice = price.toLocaleString("vi-VN", {
      style: "currency",
      currency: "VND",
    });
  }
  return formattedPrice;
};

export const formatPriceStringVND = (price) => {
  return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

export const formatDatetime = (datetimeStr) => {
  if (!datetimeStr) return "";
  const date = new Date(datetimeStr);
  return format(date, "dd/MM/yyyy 'lúc' HH:mm:ss");
};
