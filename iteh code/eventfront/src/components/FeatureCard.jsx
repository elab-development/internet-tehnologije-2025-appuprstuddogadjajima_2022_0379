import React from "react";

const FeatureCard = ({ icon: Icon, title, description }) => {
  return (
    <div className="feature-card">
      <Icon className="feature-icon" />
      <h3>{title}</h3>
      <p>{description}</p>
    </div>
  );
};

export default FeatureCard;

