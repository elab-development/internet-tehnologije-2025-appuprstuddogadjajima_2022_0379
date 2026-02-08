import React from "react";
import { FaEye } from "react-icons/fa";
import { FaEyeSlash } from "react-icons/fa";

const TextInput = ({
  type = "text",
  placeholder,
  value,
  onChange,
  required = false,
  showPasswordToggle = false,
}) => {
  const [showPassword, setShowPassword] = React.useState(false);
  const isPassword = type === "password";

  const inputType =
    isPassword && showPasswordToggle
      ? showPassword
        ? "text"
        : "password"
      : type;

  return (
    <div className="input-wrapper">
      <input
        type={inputType}
        placeholder={placeholder}
        value={value}
        onChange={onChange}
        required={required}
        className="text-input"
      />

    {isPassword && showPasswordToggle && (
  <button
    type="button"
    aria-label={showPassword ? "Sakrij lozinku" : "Prikaži lozinku"}
    className="password-toggle"
    onClick={() => setShowPassword((prev) => !prev)}
  >
    {showPassword ? <FaEyeSlash /> : <FaEye />}
  </button>
)}
    </div>
  );
};

export default TextInput;