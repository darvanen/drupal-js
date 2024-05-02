import React from "react";

interface CalendarIconProps {
  icon: string | React.ReactNode;
  className?: string;
  onClick?: (e: React.MouseEvent) => void;
}

/**
 * `CalendarIcon` is a React component that renders an icon for a calendar.
 * The icon can be a string representing a CSS class, a React node, or a default SVG icon.
 *
 * @component
 * @prop  icon - The icon to be displayed. This can be a string representing a CSS class or a React node.
 * @prop  className - An optional string representing additional CSS classes to be applied to the icon.
 * @prop  onClick - An optional function to be called when the icon is clicked.
 *
 * @example
 * // To use a CSS class as the icon
 * <CalendarIcon icon="my-icon-class" onClick={myClickHandler} />
 *
 * @example
 * // To use a React node as the icon
 * <CalendarIcon icon={<MyIconComponent />} onClick={myClickHandler} />
 *
 * @returns  The `CalendarIcon` component.
 */
const CalendarIcon = ({ icon, className = "", onClick }: CalendarIconProps) => {
  const defaultClass = "react-datepicker__calendar-icon";

  if (typeof icon === "string") {
    return (
      <i
        className={`${defaultClass} ${icon} ${className}`}
        aria-hidden="true"
        onClick={onClick}
      />
    );
  }

  if (React.isValidElement(icon)) {
    // Because we are checking that typeof icon is string first, we can safely cast icon as React.ReactElement on types level and code level
    return React.cloneElement(icon as React.ReactElement, {
      className: `${icon.props.className || ""} ${defaultClass} ${className}`,
      onClick: (e: React.MouseEvent) => {
        if (typeof icon.props.onClick === "function") {
          icon.props.onClick(e);
        }

        if (typeof onClick === "function") {
          onClick(e);
        }
      },
    });
  }

  // Default SVG Icon
  return (
    <svg
      className={`${defaultClass} ${className}`}
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 448 512"
      onClick={onClick}
    >
      <path d="M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z" />
    </svg>
  );
};

export default CalendarIcon;
