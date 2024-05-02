import React from "react";
import { clsx } from "clsx";
import type { LocaleObj } from "./date_utils";
import {
  addMonths,
  formatDate,
  getStartOfMonth,
  newDate,
  isAfter,
  isSameMonth,
  isSameYear,
  getTime,
} from "./date_utils";

function generateMonthYears(minDate: Date, maxDate: Date): Date[] {
  const list = [];

  let currDate = getStartOfMonth(minDate);
  const lastDate = getStartOfMonth(maxDate);

  while (!isAfter(currDate, lastDate)) {
    list.push(newDate(currDate));

    currDate = addMonths(currDate, 1);
  }
  return list;
}

interface MonthYearDropdownOptionsProps {
  minDate: Date;
  maxDate: Date;
  onCancel: VoidFunction;
  onChange: (monthYear: number) => void;
  scrollableMonthYearDropdown?: boolean;
  date: Date;
  dateFormat: string;
  locale?: string | LocaleObj;
}

interface MonthYearDropdownOptionsState {
  monthYearsList: Date[];
}

export default class MonthYearDropdownOptions extends React.Component<
  MonthYearDropdownOptionsProps,
  MonthYearDropdownOptionsState
> {
  constructor(props: Readonly<MonthYearDropdownOptionsProps>) {
    super(props);

    this.state = {
      monthYearsList: generateMonthYears(
        this.props.minDate,
        this.props.maxDate,
      ),
    };
  }

  renderOptions = (): JSX.Element[] => {
    return this.state.monthYearsList.map<JSX.Element>(
      (monthYear: Date): JSX.Element => {
        const monthYearPoint = getTime(monthYear);
        const isSameMonthYear =
          isSameYear(this.props.date, monthYear) &&
          isSameMonth(this.props.date, monthYear);

        return (
          <div
            className={
              isSameMonthYear
                ? "react-datepicker__month-year-option--selected_month-year"
                : "react-datepicker__month-year-option"
            }
            key={monthYearPoint}
            onClick={this.onChange.bind(this, monthYearPoint)}
            aria-selected={isSameMonthYear ? "true" : undefined}
          >
            {isSameMonthYear ? (
              <span className="react-datepicker__month-year-option--selected">
                ✓
              </span>
            ) : (
              ""
            )}
            {formatDate(monthYear, this.props.dateFormat, this.props.locale)}
          </div>
        );
      },
    );
  };

  onChange = (monthYear: number) => this.props.onChange(monthYear);

  handleClickOutside = (): void => {
    this.props.onCancel();
  };

  render(): JSX.Element {
    const dropdownClass = clsx({
      "react-datepicker__month-year-dropdown": true,
      "react-datepicker__month-year-dropdown--scrollable":
        this.props.scrollableMonthYearDropdown,
    });

    return <div className={dropdownClass}>{this.renderOptions()}</div>;
  }
}
