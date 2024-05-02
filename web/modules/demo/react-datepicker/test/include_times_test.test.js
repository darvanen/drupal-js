import React from "react";
import { render } from "@testing-library/react";
import * as utils from "../src/date_utils";
import TimeComponent from "../src/time";

describe("TimeComponent", () => {
  let today, includeTimes;

  beforeEach(() => {
    today = utils.getStartOfDay(utils.newDate());
    includeTimes = [
      utils.addMinutes(today, 60),
      utils.addMinutes(today, 120),
      utils.addMinutes(today, 150),
    ];
  });

  it("should only enable times specified in includeTimes props", () => {
    const { container: timeComponent } = render(
      <TimeComponent includeTimes={includeTimes} />,
    );

    const allTimeItems = timeComponent.querySelectorAll(
      ".react-datepicker__time-list-item",
    );
    const disabledTimeItems = timeComponent.querySelectorAll(
      ".react-datepicker__time-list-item--disabled",
    );

    const expectedDisabledTimeItems = allTimeItems.length - includeTimes.length;
    expect(disabledTimeItems.length).toBe(expectedDisabledTimeItems);
  });

  it("should not add aria-disabled attribute on all the enabled times", () => {
    const { container: timeComponent } = render(
      <TimeComponent includeTimes={includeTimes} />,
    );

    const allTimeItems = timeComponent.querySelectorAll(
      ".react-datepicker__time-list-item",
    );
    const enabledTimeItems = Array.from(allTimeItems).filter(
      (timeItem) =>
        !timeItem.classList.contains(
          "react-datepicker__time-list-item--disabled",
        ),
    );

    const enabledTimeItemsHasNoAriaDisabled = Array.from(
      enabledTimeItems,
    ).every((timeItem) => {
      const ariaDisabledValue = timeItem.getAttribute("aria-disabled");
      return !ariaDisabledValue || ariaDisabledValue.toLowerCase() === "false";
    });
    expect(enabledTimeItemsHasNoAriaDisabled).toBe(true);
  });

  it("should factor in seconds", () => {
    const includeHoursMinutesSeconds = [
      utils.addHours(utils.addSeconds(today, 30), 1), //01:00:30
      utils.addSeconds(today, 30), //00:00:30
    ];
    const { container: timeComponent } = render(
      <TimeComponent
        format="HH:mm:ss"
        includeTimes={includeHoursMinutesSeconds}
      />,
    );

    const disabledTimeItems = timeComponent.querySelectorAll(
      ".react-datepicker__time-list-item--disabled",
    );

    // 01:00:00 and 00:00:00 should be correctly disabled because they are not included
    expect(
      Array.from(disabledTimeItems).map((node) => node.textContent),
    ).toContain("01:00:00");
    expect(
      Array.from(disabledTimeItems).map((node) => node.textContent),
    ).toContain("00:00:00");
  });
});
